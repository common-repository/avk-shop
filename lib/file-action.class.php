<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */

class FileActionAVK{
    private $mainDirPath;
    private $nameMainDir;
    private $nameFile;
    const SLUG = "avkshopweb20";
    
    public function __construct(){
        
        $temp = get_option( 'avkshopweb20-settings' );
        $this->mainDirPath = $temp['main_path_up_dir'];
        $this->nameMainDir = $temp['name_up_dir'];
        $this->path = $this->mainDirPath . '/' . $this->nameMainDir;
        
        if($_POST['actionavk'] == 'addfile'){
            $this->action_upload_file();
            $result = json_encode( $this->result );
            exit( $result );
        }
        
        if($_POST['actionavk'] == 'delfile'){
            $this->action_delete_file();
            $result = json_encode( $this->result );
            exit( $result );
        }
        
    }
    
    
    private function action_upload_file(){
        if(!$this->chek_dir()) return;
        if(!$this->inspection_rights_directory()) return;
        if(!$this->create_regulations()) return;
        $this->upload_new_file();
    }
    
    private function action_delete_file(){
        $link = $this->path .'/'. trim(strip_tags($_POST['namefiledel']));
        if(file_exists($link)){
            if(unlink($link)){
                $this->result = array('type'=>'finish', 'messeg'=>'');
            }else{
                $this->result = array('type'=>'error', 'messeg'=>__('Не удалось удалить файл!',self::SLUG));
            }
        }else{
            $this->result = array('type'=>'finish', 'messeg'=>'');
        }
    }
    
    /** Проверка/создание директории */
    protected function chek_dir(){
        if(empty($this->nameMainDir)){
            $this->result = array('type'=>'error', 'messeg'=>__('Вы не ввели название директории в поле «Основной каталог», на странице «Главные настройки».', self::SLUG));
            return false;
        }
        if(!is_dir($this->path)){
            if(@mkdir($this->path, 0755)){
                $f = fopen($this->path.'/index.php', 'w+');
                fclose($f);
            }else{
                $this->result = array('type'=>'error', 'messeg'=>sprintf(__('Не удалось создать корневую директорию «%s».', self::SLUG),strtoupper($this->nameMainDir)));
                return false;
            }
        }
        return true;
    }
    
    /** Установка прав на директорию */
    protected function inspection_rights_directory(){
        $temp =  substr(sprintf('%o', fileperms($this->path)), -4);
        if( (int)$temp >= 755 ){
            return true;
        }else{
            if(!chmod($this->path,0755)){
                $this->result = array('type'=>'error', 'messeg'=>sprintf(__('Установите на директорию «%s» права 0755.', self::SLUG),strtoupper($this->nameMainDir)));
                return false;
            }
        }
    }

    /** Создание файла .htaccess */
    protected function create_regulations(){
        $this->newFileExt = pathinfo($this->path.'/'.$_FILES['avk_file']['name'], PATHINFO_EXTENSION);
        //проверка наличия расширения у файла
        if(empty($this->newFileExt)){
            $this->result = array('type'=>'error', 'messeg'=>__('Нельзя загрузить файл без расширения!!!', self::SLUG));
            return false;
        }
        $temp[] = $this->newFileExt;
        //зачитываем и размещаем в массиве все расширения файлов находящиеся в директории
        $d = dir($this->path);
        while (false !== ($entry = $d->read())){
            if($entry == '.' || $entry == '..' || $entry == '.htaccess') continue;
            if($_FILES['avk_file']['name'] == $entry and empty($_POST['newnameuploadfileavk'])){
                $this->result = array('type'=>'error', 'messeg'=>__('Файл с таким именем уже существует!!!', self::SLUG));
                return false;
            }else{
                $temp[] = pathinfo($this->path.'/'.$entry, PATHINFO_EXTENSION);
            }
        }
        $d->close();
        $temp = array_unique($temp);
        $file = $this->path . '/.htaccess';
        $strExs = implode("|", $temp);
        
        $htac = "<IfModule mod_rewrite.c>\r\n";
        $htac .= "\tOptions -Indexes FollowSymLinks\r\n";
        $htac .= "\tRewriteEngine On\r\n";
        $htac .= "\tRewriteBase /\r\n";
        $htac .= "\tRewriteRule .({$strExs})$ %{HTTP_HOST} [L]\r\n";
        $htac .= "</IfModule>";
        
        if(false === file_put_contents($file, $htac, LOCK_EX)){
            $this->result = array('type'=>'error', 'messeg'=>__('Не удалось записать файл .HTACCESS, попробуйте заново загрузить файл!', self::SLUG));
            return false;
        }
        return true;
    }
    
    
    /** Проверяет max допустимый размер файла */
    protected function max_size_file_upload(){
        $maxSize = trim(ini_get('upload_max_filesize'));
            $last = strtolower($maxSize[strlen($maxSize)-1]);
            switch($last){
                case 'g': $maxSize *= 1024;
                case 'm': $maxSize *= 1024;
                case 'k': $maxSize *= 1024;
            }
        return $maxSize;
    }
    
    /** Загрузка файла */
    protected function upload_new_file(){
        if ($_FILES['avk_file']['error'] === UPLOAD_ERR_OK){
            $maxSizeFile = $this->max_size_file_upload();
            if ($_FILES['avk_file']['size'] < $maxSizeFile and $_FILES['avk_file']['size']!=0){
                $this->nameFile = $this->sanitize_name_file(trim(strip_tags($_FILES['avk_file']['name'])));
                $pathFile = $this->path . '/' . $this->nameFile;
                if (move_uploaded_file($_FILES['avk_file']['tmp_name'], $pathFile)){
                    $nameFile = explode( ".", $this->nameFile );
                    $this->result = array( 'type' => 'finish', 'name' => $this->nameFile, 'newname' => $nameFile[0] );
                    return true;
                }else{
                    $this->result = array('type'=>'error', 'messeg'=>__('Не удалось загрузить файл!',self::SLUG));
                    return false;
                }
            }else{
                $this->result = array('type'=>'error', 'messeg'=>__('Недопустимый размер файла!',self::SLUG));
                return false;
            }
        }else{
            $error_values = array( UPLOAD_ERR_INI_SIZE   => 1,
                                   UPLOAD_ERR_FORM_SIZE  => 2,
                                   UPLOAD_ERR_PARTIAL    => 3, 
                                   UPLOAD_ERR_NO_FILE    => 4, 
                                   UPLOAD_ERR_NO_TMP_DIR => 5, 
                                   UPLOAD_ERR_CANT_WRITE => 6 );  
            $error_code = $_FILES['avk_file']['error'];      
            if (!empty($error_values[$error_code])){
                $errorNum = $error_values[$error_code];
            }else{
                $errorNum = 7;
            }
            switch($errorNum){
                case '1': $message = __('Размер файла больше разрешенного директивой upload_max_filesize в php.ini.',self::SLUG); break;
                case '2': $message = __('Размер файла превышает указанное значение в MAX_FILE_SIZE.',self::SLUG); break;
                case '3': $message = __('Файл был загружен только частично.',self::SLUG); break;
                case '4': $message = __('Не был выбран файл для загрузки.',self::SLUG); break;
                case '5': $message = __('Не найдена папка для временных файлов.',self::SLUG); break;
                case '6': $message = __('Ошибка записи файла.',self::SLUG); break;
                case '7': $message = __('Случилось что-то непонятное. Файл не загружен или загружен с ошибками. Повторите загрузку!!!',self::SLUG); break;
            }
            $this->result = array('type'=>'error', 'messeg'=>$message);
            return false;
        }
    }
    
    protected function sanitize_name_file($title){
        $iso = array("Є"=>"YE","І"=>"I","Ѓ"=>"G","і"=>"i","№"=>"#","є"=>"ye",
             "ѓ"=>"g","А"=>"A","Б"=>"B","В"=>"V","Г"=>"G","Д"=>"D",
             "Е"=>"E","Ё"=>"YO","Ж"=>"ZH","З"=>"Z","И"=>"I","Й"=>"J",
             "К"=>"K","Л"=>"L","М"=>"M","Н"=>"N","О"=>"O","П"=>"P","Р"=>"R",
             "С"=>"S","Т"=>"T","У"=>"U","Ф"=>"F","Х"=>"X","Ц"=>"C","Ч"=>"CH",
             "Ш"=>"SH","Щ"=>"SHH","Ъ"=>"'","Ы"=>"Y","Ь"=>"","Э"=>"E","Ю"=>"YU",
             "Я"=>"YA","а"=>"a","б"=>"b","в"=>"v","г"=>"g","д"=>"d","е"=>"e",
             "ё"=>"yo","ж"=>"zh","з"=>"z","и"=>"i","й"=>"j","к"=>"k","л"=>"l",
             "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r","с"=>"s","т"=>"t","у"=>"u",
             "ф"=>"f","х"=>"x","ц"=>"c","ч"=>"ch","ш"=>"sh","щ"=>"shh","ъ"=>"",
             "ы"=>"y","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","—"=>"-","_"=>"_","«"=>"","»"=>"","…"=>""," "=>"-");
             
        return strtr($title, $iso);
    }
    
    public function __destruct(){
        
    }
}