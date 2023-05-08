<?

class PDFEngine
{
    private $_PDFVersion = '';
    /**
     * Путь к PDF файлу
     * @var null
     */
    private $filePath = NULL;
    /**
     * Указатель на открытый файл
     * @var null|resource
     */
    public $filePointer = NULL;
    /**
     * Размер файла
     * @var int
     */
    private $fileSize = 0;
    /**
     * Номер текущей страницы
     * @var int
     */
    private $currentPage = 0;
    /**
     * Таблица перекрестных ссылок.<br />
     * Ключ массива -- Номер PDF Объекта <br />
     * Значение -- Адрес началала объекта в файле. Массив отсортирован по значению.
     * @var array
     */
    public $RefTable = array();
    /**
     * Костыль для Таблицы перекрестных ссылок. <br />
     * Ключ массива -- Номер PDF Объекта <br />
     * Значение -- Адрес конца объекта в файле.<br />
     * Костыль сделан потому, что PHP не позволяет узнать следующий элемент массива иначе, как пробегая каждый раз
     * от первого элемента до искомого, а так как это была бы слишком частая операция, было решено пожертвовать
     * некоторым количеством памяти в обмен на производительность
     * @var array
     */
    public $RefTableNext = array();
    /**
     * Таблица страниц<br />
     * Ключ массива -- номер страницы<br />
     * Значение -- номер объекта в PDF соответствующий данной странице
     * @var array
     */
    public $pageTable = array();

    /**
     * В конструкторе Выполняется подключение к файлу, сохраняется его путь и раззмер.
     * Так же в конструкторе создается перекрестная таблица ссылок для быстрого ддоступа к объектам, не загружая
     * при этом весь файл в память. Здесь же создается таблица для бысрого доступа к страницам.
     * @param $file
     */
    public function __construct($file)
    {
        $this->filePath = $file;
        $this->filePointer = fopen($this->filePath, 'rb');
        $this->fileSize = filesize($this->filePath);
        $this->get_ref_table();
        $this->get_page_table();
    }

    /**
     * Просто закрывает за собой файл
     */
    public function __destruct()
    {
        try {
            fclose($this->filePointer);
        } catch (\Exception $e) {
//Ничего не делаем потому, как по видимому файл просто уже закрыт=)
        }
    }

    /**
     * Самая нужная для пользователя функция.
     * Она возвращает данные о запрошенной странице.
     * //ToDo: Или NULL если страницы закончились
     * @param int $pageNum
     */
    public function get_page($pageNum = 1)
    {
        if (isset($this->pageTable[$pageNum - 1])) {
            $pageStart = $this->RefTable[$this->pageTable[$pageNum - 1]];
            $pageEnd = $this->RefTableNext[$this->pageTable[$pageNum - 1]];
            $page = new Page($pageStart, $pageEnd, $this);
            $page->get_text();
            $page->convert_to_paragraph();
            return $page;
        } else {
            return NULL;
        }
    }

    /**
     * <b>Функция перенесена в класс абстрактного Объекта ПДФ</b>
     * Функция парсит объект PDF типа stram. Возвращает его содержимое
     * @param $objectID
     * @return string
     * @depricated
     */
    protected function get_stream_content($objectID)
    {
        $object = $this->get_obj_by_key($objectID);
        preg_match('/\/Filter\[?\s?(.*)\]?\W/', $object, $matches);
        preg_match('/(\s?\/(\w+))+/', $matches[1], $filter);
        preg_match('/stream(.*)endstream/ismU', $object, $streamcontent);
        $stream = trim($streamcontent[1]);
        if ($filter[1] = 'FlateDecode') {
            return @gzuncompress($stream);

        } else return $stream;
    }

    /**
     * Функция извлекает таблицу объектов содержащих страницы. Результаты раскидывает по свойствам объекта
     * @return bool
     */
    private function get_page_table()
    {
        $currentObj = '';

        reset($this->RefTable);
        $key = key($this->RefTable);
        $matches = NULL;
        $nextPage = NULL;
        $pages = array();
        while ((preg_match('/\x2F\x54\x79\x70\x65\x2F\x43\x61\x74\x61\x6C\x6F\x67/', $currentObj) != 1) ||
            ($currentObj === false)
        ) {

            $currentObj = $this->get_obj_by_key($key);
            next($this->RefTable);
            $key = key($this->RefTable);
        }
        preg_match('/\x2F\x50\x61\x67\x65\x73\x20(\d+)\x20\x30\x20\x52/', $currentObj, $matches);
        $currentObj = $this->get_obj_by_key($matches[1]);
        preg_match('/\x2FKids\[(.*)\]/', $currentObj, $kids);
        preg_match_all('/\s?(\d+)\s\d+\sR/', $kids[1], $matches);
        foreach ($matches[1] as $value) {
            $pages[] = $value;
        }
        foreach ($pages as $key => $value) {
            if (isset($this->RefTable[$value])) {
                $page = $this->get_obj_by_key($value);
                if (preg_match('/\/Type\/Page\W/', $page) == 1) {
                    $this->pageTable[] = $value;
                }
                $this->pageTable = array_merge($this->pageTable, $this->getChildren($value));
            }
        }
        return true;
    }

    /**
     * Получить все дочерние страницы.
     * @param $Obj
     * @return array
     */
    private function getChildren($Obj)
    {
        $pages = array();
        $pagesArr = array();
        $currentObj = $this->get_obj_by_key($Obj);
        preg_match('/\x2FKids\[(.*)\]/', $currentObj, $kids);
        if (isset($kids[1])) {
            if (preg_match_all('/\s?(\d+)\s\d+\sR/', $kids[1], $matches) > 0) {
                foreach ($matches[1] as $value) {
                    $pages[] = $value;
                }
                foreach ($pages as $key => $value) {
                    if (isset($this->RefTable[$value])) {
                        $page = $this->get_obj_by_key($value);
                        if (preg_match('/\/Type\/Page\W/', $page) == 1) {
                            $pagesArr[] = $value;
                        }
                        $pagesArr = array_merge($pagesArr, $this->getChildren($value));
                    }
                }
                return $pagesArr;
            } else return array();
        }
        return array();
    }

    /**
     * Парсинг ссылочной таблицы. На объекты.
     */
    private function get_ref_table()
    {
        $currentString = '';
        $matches = NULL;
        $tableLength = 0;
        $lastTable = false;

        fseek($this->filePointer, -32, SEEK_END);
        $nextTableLink = '';
        while (preg_match('/startxref/', $nextTableLink) != 1 && $nextTableLink !== false) {
            $nextTableLink = fgets($this->filePointer);
        }
        $nextTableLink = fgets($this->filePointer) + 0;
        while ($lastTable !== true) {
            fseek($this->filePointer, $nextTableLink, SEEK_SET);
            fgets($this->filePointer);
            $currentString = fgets($this->filePointer);
            preg_match('/(\d+)\x20(\d+)/', $currentString, $matches);
            $tableLength = $matches[2];
            $startIndex = $matches[1];
            for ($i = 0; $i < $tableLength; $i++) {
                $currentString = fgets($this->filePointer);
                preg_match('/(\d+)\x20\d+\x20\x6E/', $currentString, $matches);
                if (isset($matches[1]))
                    $this->RefTable[$startIndex + $i] = $matches[1];
            }
            fgets($this->filePointer);
            $currentString = fgets($this->filePointer);
            if (preg_match('/\x2FPrev\x20(\d+)/', $currentString, $matches) == 1)
                $nextTableLink = $matches[1] + 0;
            else
                $lastTable = true;
        }
        asort($this->RefTable, SORT_NUMERIC);
        reset($this->RefTable);
        $pointerKey = NULL;
        foreach ($this->RefTable as $key => $value) {
            if ($pointerKey != NULL)
                $this->RefTableNext[$pointerKey] =& $this->RefTable[$key];
            $pointerKey = $key;
        }
        if ($pointerKey != NULL)
            $this->RefTableNext[$pointerKey] = $nextTableLink;
    }

    /**
     * <b>Функция перенесена в класс абсрактного объекта</b>
     * Получение объекта по ключу(По адресу из перекрестной таблицы ссылок)
     * @param $key
     * @return string
     * @depricated
     */
    public function get_obj_by_key($key)
    {
        fseek($this->filePointer, $this->RefTable[$key]);
        return fread($this->filePointer, $this->RefTableNext[$key] - $this->RefTable[$key]);
    }

    private function get_bin_obj($key)
    {
        return file_get_contents($this->filePath, FILE_BINARY, NULL, $this->RefTable[$key], $this->RefTableNext[$key] - $this->RefTable[$key]);
    }

}

/**
 * Это не Абстрактный класс объекта, а класс Абстрактного объекта.<br />
 * В него включены методы для более удобной работы с объектами.<br />
 * Все ыажные данные объекта должны храниться в массиве data[]. например: <br />data['ParmName']=value<br />
 * К таким "переменным" можно получить доступ извне только для чтения благодаря магическому __GET()<br />
 * $object->ParamName;<br />
 *
 */
class AbstractPDFObject
{
    protected $properties = array();
    protected $data = array();
    protected $resources;

    protected $parentPDFEngine = NULL;

    public function __construct($pageStart, $PageEnd, &$PDFEngine)
    {
        if (!isset($this->parentPDFEngine))
            $this->parentPDFEngine = $PDFEngine;
        $this->parse_properties($pageStart, $PageEnd, $this->parentPDFEngine->filePointer);
    }

    /**
     * получить элемент массива data[]
     * @param $paramType
     * @return null
     */
    public function get_data($paramType)
    {
        if (isset($this->data[$paramType])) {
            return $this->data[$paramType];
        } else return NULL;
    }

    /**
     * Магический $__get возвращает элемент из data
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get_data($name);
    }

    /**
     * Устанавливает значение в массив data
     * @param $paramType
     * @param $paramValue
     * @return AbstractPDFObject
     */
    public function set_data($paramType, $paramValue)
    {
        if (!is_scalar($paramType) && !is_scalar($paramValue)) {
            foreach ($paramType as $key => $name) {
                $this->data[$name] = $paramValue[$key];
            }
        } elseif (is_scalar($paramType) && is_scalar($paramValue))
            $this->data[$paramType] = $paramValue;
        return $this;
    }

    /**
     * Устанавливает значение в массив propertis
     * @param $paramType
     * @param $paramValue
     * @return AbstractPDFObject
     */
    public function set_property($paramType, $paramValue)
    {
        if (!is_scalar($paramType) && !is_scalar($paramValue)) {
            foreach ($paramType as $key => $name) {
                $this->properties[$name] = $paramValue[$key];
            }
        } elseif (is_scalar($paramType) && is_scalar($paramValue))
            $this->properties[$paramType] = $paramValue;
        return $this;
    }

    /**
     * Получить элемент из массива propertis
     * @param $paramType
     * @return null
     */
    public function get_property($paramType)
    {
        if (isset($this->properties[$paramType])) {
            return $this->properties[$paramType];
        } else return NULL;
    }

    /**
     * Функция достает из объекта тест PDF dictionry и сохраняет его в $this->data['propertiesString']
     * Так же эта функция извлекает поток объекта и по возможности декодирует. В любом случае сохраняет его в
     * $this->data['stream']
     * @param $startObj
     * @param $endObj
     */
    protected function parse_properties($startObj, $endObj, $file)
    {
        $this->data['stream'] = '';
        fseek($file, $startObj);
        $fullObjContent = fread($file, $endObj - $startObj);
        preg_match('|<<(.*)>>|', $fullObjContent, $matches);
        if (isset($matches[1]))
            $this->data['propertiesString'] = $matches[1];
        $this->data['stream'] .= $this->decode_stream($fullObjContent);
        preg_match('|/Contents ?\[?(( ?\d+ \d+ R ?)+)\]?|', $this->data['propertiesString'], $matches);
        if (isset($matches[1])) {
            $contentsObj = $matches[1];
            preg_match_all('|(\d+) \d+ R|', $contentsObj, $matches);
            foreach ($matches[1] as $value) {
                $fullObjContent = $this->parentPDFEngine->get_obj_by_key($value);
                if (isset($matches[1]))
                    $this->data['stream'] .= $this->decode_stream($fullObjContent);
            }
        }
    }

    protected function parse_resurce()
    {
        $this->data['resources'] = '';
        preg_match('|/Resources ?\[?(( ?\d+ \d+ R ?)+)\]?|ims', $this->data['propertiesString'], $matches);
        if (isset($matches[1])) {
            $ResourceObj = $matches[1];
            preg_match_all('|(\d+) \d+ R|', $ResourceObj, $matches);
            foreach ($matches[1] as $value) {
                $this->data['resources'] .= $this->parentPDFEngine->get_obj_by_key($value);
            }
        }
    }

    /**
     *  Декодирует поток. Прежде чем запускать: необходимо текст
     * свойств объекта поместить в $this->data['propertiesString']
     * @param $stream
     * @return string
     */
    protected function decode_stream($objString)
    {
        preg_match('|stream(.*)endstream|ms', $objString, $matches);
        if (isset($matches[1])) {
            $stream = trim($matches[1]);
            preg_match('|/Filter ?\[(.*)\]|', $objString, $matches);
            if (isset($matches[1])) {
                $filter = $matches[1];
                preg_match_all('|(/\w+)|', $filter, $matches);
                foreach ($matches[1] as $value) {
                    if ($value == '/FlateDecode') {
                        return gzuncompress($stream);
                    }
                }
            } else {
                preg_match('|/Filter ?/FlateDecode|', $objString, $matches);
                if (isset($matches[0])) {
                    return gzuncompress($stream);
                } else {
                    return $stream;
                }
            }
            return $stream;
        }
        return '';
    }
}

/**
 * Объект страницы PDF
 * Частный случай Абстрактного объекта. Извлекает все данные о странице: Текст, шрифты, изображения, оглавления и т д
 */
class Page extends AbstractPDFObject
{
    /**
     * Конструктор. При инициализации обзекта находит все ресурсы объекта и парсит их в атомарные для данной абстракции
     * элементы:строки, изображения и т д
     * Получает инстанс PDF обработчика
     * @param $pageNum
     * @param $PDFEngine
     */
    public function __construct($pageStart, $PageEnd, &$PDFEngine)
    {
        $this->parentPDFEngine = $PDFEngine;
        $this->parse_properties($pageStart, $PageEnd, $this->parentPDFEngine->filePointer);
        $this->parse_resurce();
        $this->get_fonts_obj();
    }

    public function get_fonts_obj()
    {
        preg_match_all('|/Font ?<{0,2}(( ?/[0-9a-z]+ \d+ \d+ R ?)+)>{0,2} ?|i', $this->propertiesString, $fontsString);
        if (!isset($fontsString[1][0])) {
            preg_match_all('|/Font ?<{0,2}(( ?/[0-9a-z]+ \d+ \d+ R ?)+)>{0,2} ?|i', $this->data['resources'], $fontsString);
        }
        preg_match_all('| ?(/[0-9a-z]+) (\d+) \d+ R ?|i', $fontsString[1][0], $matches);
        foreach ($matches[1] as $key => $value) {
            $this->data['Fonts'][$value]['TrnsfrmTable'] = array();
            $this->data['Fonts'][$value]['objString'] = $this->parentPDFEngine->get_obj_by_key($matches[2][$key]);
//Ищем свойство /ToUnicode Если его нет, значит строку можно будет писать "как есть". Если есть, то там будет таблица замещения
            preg_match('|/ToUnicode (\d+) \d+ R|', $this->data['Fonts'][$value]['objString'], $toUnicodeString);
            if (isset($toUnicodeString[1])) {
//Если /toUnicode существует, то находим соответствующий ему объект и декодируем поток. Получаем данные
                $this->data['Fonts'][$value]['ToUnicode'] = $this->decode_stream($this->parentPDFEngine->get_obj_by_key($toUnicodeString[1]));
//извлекаем блоки для замены одиночных символов
                preg_match_all('|beginbfchar(.*?)endbfchar|ms', $this->data['Fonts'][$value]['ToUnicode'], $SinglCharTrnsfrmBlock);
                foreach ($SinglCharTrnsfrmBlock[1] as $blockData) {
                    preg_match_all('|<([0-9a-f]{2,4})> +<([0-9a-f]{4,512})> *$|ims', $blockData, $SinglCharTrnsfrm);
                    foreach ($SinglCharTrnsfrm[1] as $tuUnicKey => $toUnicValue) {
                        $SinglCharTrnsfrm[1][$tuUnicKey] = strtolower("\x" . substr_replace($SinglCharTrnsfrm[1][$tuUnicKey], "\x", 2, 0));
                        $SinglCharTrnsfrm[2][$tuUnicKey] = mb_convert_encoding(chr(hexdec(substr($SinglCharTrnsfrm[2][$tuUnicKey], 0, 2)))
                            . chr(hexdec(substr($SinglCharTrnsfrm[2][$tuUnicKey], -2))), "UTF-8", "UTF-16");
                    }
                    $this->data['Fonts'][$value]['TrnsfrmTable'] = array_merge($this->data['Fonts'][$value]['TrnsfrmTable'],
                        array_combine($SinglCharTrnsfrm[1], $SinglCharTrnsfrm[2]));
                }
//Извлекаем блоки с диапозонами для замены
                if (preg_match_all('|beginbfrange(.*?)endbfrange|ims', $this->data['Fonts'][$value]['ToUnicode'], $CharRangeTrnsfrmBlock)) {
                    foreach ($CharRangeTrnsfrmBlock[1] as $blockData) {
//разбираем первый способ записи диапозона (см документацию на PDF ISO_32000_1_2008 страница 294)
                        if (preg_match_all('|<([0-9a-f]{4})>\s+<([0-9a-f]{4})>\s+<([0-9a-f]{4})> *$|imsU', $blockData, $CharRangeTrnsfrm)) {
                            foreach ($CharRangeTrnsfrm[1] as $key => $CharRange) {
                                for ($i = hexdec($CharRangeTrnsfrm[1][$key]); $i <= hexdec($CharRangeTrnsfrm[2][$key]); $i++) {
                                    $from = "\x" . substr_replace(str_pad(dechex($i), 4, '0', STR_PAD_LEFT), "\x", 2, 0);
                                    $to = mb_convert_encoding(chr(hexdec(substr(str_pad($CharRangeTrnsfrm[3][$key], 4, '0', STR_PAD_LEFT), 0, 2)))
                                        . chr(hexdec(substr(str_pad($CharRangeTrnsfrm[3][$key], 4, '0', STR_PAD_LEFT), -2))), "UTF-8", "UTF-16");
                                    $this->data['Fonts'][$value]['TrnsfrmTable'][strtolower($from)] = $to;
                                    $CharRangeTrnsfrm[3][$key] = str_pad(dechex(hexdec($CharRangeTrnsfrm[3][$key]) + 1), 4, '0', STR_PAD_LEFT);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function get_text()
    {
        $this->data['clearText'] = '';
        $this->data['logicBlocks'] = array();
        $textBlockCount = 0;
        $prevFont = NULL;
        $currentLogicalBlock = NULL;
//Разбиваем весь поток на PDF-блоки текста
        preg_match_all('|^(.*?)BT(.*?)ET|ms', $this->stream, $dirtyTextBlocks);
//ToDo: Дальше -- ГОВНОКОД. Подумать и исправить, чтоб работало быстреее.
        foreach ($dirtyTextBlocks[2] as $blockNum => $blockString) {
//Получаем Шрифты и PDFмассив блоков текста. $fontAndText[1] -- шрифт, $fontAndText[2] -- набор текстовых блоков
            if (preg_match_all("|([\r\n]{1,2})?((/[0-9a-z]+)\s\d+\.?\d*\sTf)?.*\[(.*)\]|ism", $blockString, $fontAndText)) {
//Пишем в следующий текстовый блок.
                $textBlockCount++;
//получим чистый массив логических блоков

                if (isset($dirtyTextBlocks[1][$blockNum]) && $dirtyTextBlocks[1][$blockNum] != "" &&
                    $currentLogicalBlock !== $blockNum) {
                    $this->data['logicBlocks'][$textBlockCount] = $dirtyTextBlocks[1][$blockNum];
                    $currentLogicalBlock = $blockNum;
                }
//перебираем текстовые блоки(и шрифты к ним)
                foreach ($fontAndText[4] as $key => $textBlocks) {
                   // $this->data['clearText'][$textBlockCount]['text'] = "";
                    if ($fontAndText[3][$key]) {
                        $prevFont = $fontAndText[3][$key];
                    } else {
                        $fontAndText[3][$key] = $prevFont;
                    }
//Вынимаем два типа блоков текста: HEX-строки в <> и текстовые строки в ()
                    preg_match_all('#[<\(].*?[\)>]#', $fontAndText[4][$key], $textBlock);
//перебираем блоки
                    foreach ($textBlock[0] as $data) {
//Если блок с HEX  Значениями , то
                        if (preg_match('#<(.*?)>#', $data, $hexString)) {
//Если строку надо декодировать
                            if (isset($this->data['Fonts'][$fontAndText[3][$key]]['ToUnicode'])) {
                                while ($hexString[1]) {
//Если есть значение в таблице декодирования
                                    if (isset($this->data['Fonts'][$fontAndText[3][$key]]['TrnsfrmTable']["\x" . substr_replace(substr(strtolower($hexString[1]), 0, 4), "\x", 2, 0)])) {
                                       // $this->data['clearText'][$textBlockCount]['text'] .= $this->data['Fonts'][$fontAndText[3][$key]]['TrnsfrmTable']["\x" . substr_replace(substr(strtolower($hexString[1]), 0, 4), "\x", 2, 0)];
                                    } //Если такого значения нет
                                    else {
                                       // $this->data['clearText'][$textBlockCount]['text'] .= mb_convert_encoding(chr(hexdec(substr(substr(strtolower($hexString[1]), 0, 4), 0, 2)))
                                        //    . chr(hexdec(substr(substr(strtolower($hexString[1]), 0, 4), -2))), "UTF-8", "UTF-16");
                                    }
                                    $hexString[1] = substr($hexString[1], 4);
                                }
//Если не надо декодировать
                            } else {
                                while ($hexString[1]) {
                                    $this->data['clearText'][$textBlockCount]['text'] .= mb_convert_encoding(chr(hexdec(substr(substr(strtolower($hexString[1]), 0, 4), 0, 2)))
                                        . chr(hexdec(substr(substr(strtolower($hexString[1]), 0, 4), -2))), "UTF-8", "UTF-16");
                                    $hexString[1] = substr($hexString[1], 4);
                                }
                            }
                        } //Если блок с обычными строками
                        else {
                            $fontAndText[4][$key] = preg_replace('|\((.*)\)|', '$1', $fontAndText[4][$key]);
                            if (isset($this->data['Fonts'][$fontAndText[3][$key]]['ToUnicode'])) {
                                $transformedText = '';
                                foreach ($this->data['Fonts'][$fontAndText[3][$key]]['TrnsfrmTable'] as $from => $to) {
//$this->data['clearText'] .= str_replace('\uccc'.$from,'\ucccc'.$to, $fontAndText[2][$key]);
//$this->data['clearText'] .= preg_replace('|\x{'.$from.'}|u','\x{'.$to.'}',$fontAndText[2][$key]);
                                    $transformedText = preg_replace("|" . $from . "|", $to, $fontAndText[4][$key]);
                                }
                                $this->data['clearText'][$textBlockCount]['text'] .= $transformedText;
                            } else {
                               // $this->data['clearText'][$textBlockCount]['text'] .= $fontAndText[4][$key];
                            }
                        }
                    }
                }
            }
        }
    }

    public function convert_to_paragraph()
    {
        $this->data['clearParagraph'] = array();
        $paragraphCounter = 0;
        $firstBlock = NULL;
        foreach ($this->data['logicBlocks'] as $blockNum => $logicValue) {
            if (!isset($firstBlock)) {
                $firstBlock = $blockNum;
            } else {
                $paragraphCounter++;
                $this->data['clearParagraph'][$paragraphCounter] = '';
                for ($firstBlock; $firstBlock < $blockNum; $firstBlock++) {
                    $this->data['clearParagraph'][$paragraphCounter] .= $this->data['clearText'][$firstBlock]['text'];
                }
            }
        }
    }

}