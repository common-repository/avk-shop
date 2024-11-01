<?php
/**
 * @author Smiling_Hemp
 * @copyright 2013
 */

class AVKShopVariables extends MainValueAvkShop{
    public $HTML;
    public $LIB;
    protected $HOST, $urlHOST;
    protected $DIR;
    protected $uploadDir;
    protected $baseNameFile;
    protected $submenuPage, $submenuBar;
    protected $USER;
    protected $statusMsg;
    protected $warningMsg;
    protected $defaultMainSettings;
    protected $metaBoxValue;
    protected $city, $mobile;
    protected $queryShopping, $queryDownload;
    protected $ajaxFalse = array('type' => false);
    protected $ajaxTrue  = array('type' => true);
    protected $counterDownloads = 0;
    protected $counterCustomer = 1;
    
    
    public function __construct(){
        parent::__construct();
        global $wpdb;
        $this->HOST = $_SERVER['HTTP_HOST'];
        $this->urlHOST = get_bloginfo('url');
        $this->DIR  = dirname(plugin_basename(__DIR__));
        $this->uploadDir = wp_upload_dir();
        $this->baseNameFile = $this->DIR . '/avkshop-engine.class.php';
        $this->warningMsg = __('Введите данные', self::SLUG);
        $this->defaultMainSettings = array(
                                        array("type"=>"open","title"=>__("Настройки", self::SLUG)),
                                        array("type"=>"openfieldset", "id"=>"fildplon", "title"=>__('Основные настройки',self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"pluginon",
                                              "class"=>"select",
                                              "std" => 'off',
                                              "label"=>__("Включить плагин", self::SLUG),
                                              "option"=>array('on'=>__('Включено',self::SLUG),'off'=>__('Выключено',self::SLUG)),
                                              "desc"=>__("Для активизации всех опций плагина, необходимо его включить!", self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"ajax_system",
                                              "class"=>"select",
                                              "std" => 'off',
                                              "label"=>"WEB 2.0",
                                              "option"=>array('on'=>__('Включено',self::SLUG),'off'=>__('Выключено',self::SLUG)),
                                              "desc"=>__("Опция позволяет включить/выключить использование метода WEB 2.0 в Вашем магазине.", self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"translit",
                                              "class"=>"select",
                                              "std" => 'off',
                                              "label"=>__("Транслит постоянных ссылок", self::SLUG),
                                              "option"=>array('on'=>__('Включено',self::SLUG),'off'=>__('Выключено',self::SLUG)),
                                              "desc"=>__("Настройка включает транслит постоянных ссылок, которые формируются при создании поста", self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"currency",
                                              "class"=>"select",
                                              "std" => 'RUB',
                                              "label"=>__("Валюта", self::SLUG),
                                              "option"=>array('RUB'=>__('Российский рубль',self::SLUG),
                                                              'UAH'=>__('Украинская гривна',self::SLUG),
                                                              'USD'=>__('Доллар США',self::SLUG),
                                                              'EUR'=>__('Евро',self::SLUG)),
                                              "desc"=>__("Выберите валюту, с которой будет работать Ваш магазин", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"amount_download",
                                              "label"=>__("Количество загрузок", self::SLUG),
                                              "std" => '3',
                                              "desc"=> sprintf( __("Установите, какое количество скачиваний может произвести клиент после оплаты товара. %s По умолчанию 3 раза. %s", self::SLUG), '<br /><var>', '</var>' ) ),
                                        array("type"=>"closefieldset"),
                                        
                                        array("type"=>"openfieldset", "id"=>"fildplupdir", "important"=>"", "title"=>__('Настройки директории для файлов',self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"main_path_up_dir",
                                              "class"=>"select",
                                              "std" => "",
                                              "label"=>__("Родительский каталог", self::SLUG),
                                              "option"=>array(rtrim(ABSPATH,'/') => __('Корневая директория сайта',self::SLUG),
                                                              AVKSHOP_PL_PATH => __('Директория плагина ' . $this->name,self::SLUG),
                                                              AVKSHOP_CONT_PATH . '/plugins' => __('Директория PLUGINS',self::SLUG),
                                                              $this->uploadDir['basedir'] => __('Директория UPLOADS',self::SLUG),
                                                              AVKSHOP_CONT_PATH => sprintf( __( 'Директория %s', self::SLUG ), strtoupper( array_pop( explode( '/', content_url() ) ) ) )
                                                              ),
                                              "desc"=>__("Выберите родительский каталог, в котором будет располагаться <b>«Основной каталог»</b> с вашими файлами.", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"name_up_dir",
                                              "label"=>__("Основной каталог", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Введите любое произвольное имя для каталога, который будет хранить Ваши платные и бесплатные файлы. <br />ВНИМАНИЕ:<br /> Используйте буквы латинского алфавита, без пробелов!!!", self::SLUG)),
                                        array("type"=>"closefieldset"),
                                        
                                        array("type"=>"openfieldset", "id"=>"fildplpage", "title"=>__('Страницы для плагина',self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"start_reg",
                                              "label"=>__("Страница для регистрации", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Впишите ID страницы, которая будет использоваться для регистрации клиентов.", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"intermediate_reg",
                                              "label"=>__("Финальная страница регистрации", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Впишите ID страницы, которая будет выводиться при успешной регистрации.", self::SLUG)),
                                        /** array("type"=>"text",
                                              "id"=>"error_reg",
                                              "label"=>__("Ошибки при активации", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Впишите ID страницы, которая будет использоваться, если срок активации истек или попытка повторного использования регистрационного ключа.", self::SLUG)), */
                                        array("type"=>"text",
                                              "id"=>"authorization",
                                              "label"=>__("Страница авторизации", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Впишите ID страницы, которая будет использоваться при авторизации посетителя после того как он ввел неправильные данные при первой авторизации.", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"cart_page",
                                              "label"=>__("Корзина с товаром", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Впишите ID страницы, которая будет использоваться для вывода содержимого корзины для последующей оплаты или удаления товара из неё.", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"page_status_pay",
                                              "label"=>__("Статус платежа", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Впишите ID страницы, которая будет использоваться для вывода клиенту статуса о совершенном платеже.", self::SLUG)),
                                        array("type"=>"closefieldset"),
                                        
                                        array("type"=>"openfieldset", "id"=>"fildplsec", "important"=>"", "title"=>__('Настройки безопасности',self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"captchaenable",
                                              "label"=> "reCAPTCHA",
                                              "class"=>"select",
                                              "std" => 'on',
                                              "option"=>array('on'=>__('Использовать',self::SLUG),'off'=>__('Не использовать',self::SLUG)),
                                              "desc"=>__("Использование reCAPTCHA защищает Вас от злоумышленников желающих навредить Вашему сайту при регистрации и авторизации.", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"ownsafetysite",
                                              "label"=>__("Индивидуальная защита", self::SLUG),
                                              "std" => 'ownsafetysiteavk',
                                              "desc"=> sprintf(__("Измените фразу по умолчанию! Вписав собственную фразу, состоящую из латинского алфавита и цифр, %s БЕЗ ПРОБЕЛОВ %s !!! Это позволит использовать индивидуальную защиту для вашего сайта!", self::SLUG),'<b>','</b>')),
                                        array("type"=>"text",
                                              "id"=>"captchapublickey",
                                              "label"=>__("Публичный ключ", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Введите Публичный ключ, полученный при регистрации на сайте", self::SLUG).' <a href="https://www.google.com/recaptcha" target="_blank">reCaptcha</a>'),
                                        array("type"=>"text",
                                              "id"=>"captchasicretkey",
                                              "label"=>__("Секретный ключ", self::SLUG),
                                              "std" => '',
                                              "desc"=> __("Введите Секретный ключ, полученный при регистрации на сайте", self::SLUG).' <a href="https://www.google.com/recaptcha" target="_blank">reCaptcha</a>'),
                                        array("type"=>"select",
                                              "id"=>"chekregform",
                                              "label"=>__("Проверка формы", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'on',
                                              "option"=>array('on'=>__('Включить',self::SLUG),'off'=>__('Выключить',self::SLUG)),
                                              "desc"=>__("Опция включает проверку регистрационной формы на клиентской стороне и позволяет сократить лишние запросы к серверу. Данная опция не влияет на проверку данных на серверной стороне.", self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"tableshopping",
                                              "label"=>__("Имя таблицы покупок", self::SLUG),
                                              "std" => 'shopping_avk',
                                              "desc"=> sprintf(__("Вы можете ввести собственное имя таблицы учета покупок или оставить по умолчанию %s. После перейдите на %s страницу %s и нажмите кнопку «Создать». %s ВНИМАНИЕ!!! %s Имя таблицы должно быть уникальным", self::SLUG),'shopping_avk','<a href="'.admin_url('admin.php?page='.self::SLUG.'-action-file').'" target="_blank">','</a>','<br />','<br />')),
                                        array("type"=>"text",
                                              "id"=>"tabledownload",
                                              "label"=>__("Имя таблицы загрузок", self::SLUG),
                                              "std" => 'download_avk',
                                              "desc"=> sprintf(__("Вы можете ввести собственное имя таблицы учета загрузок или оставить по умолчанию %s. После перейдите на %s страницу %s и нажмите кнопку «Создать». %s ВНИМАНИЕ!!! %s Имя таблицы должно быть уникальным", self::SLUG),'download_avk','<a href="'.admin_url('admin.php?page='.self::SLUG.'-action-file').'" target="_blank">','</a>','<br />','<br />')),
                                        array("type"=>"select",
                                              "id"=>"restore_password",
                                              "label"=>__("Востановление пароля", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'off',
                                              "option"=>array('on'=>__('Включить',self::SLUG),'off'=>__('Выключить',self::SLUG)),
                                              "desc"=> __("Данная опция позволяет для покупателей скрыть/отобразить ссылку для восстановления пароля.", self::SLUG) ),
                                        array("type"=>"closefieldset"),
                                        
                                        array("type"=>"openfieldset", "title"=>__('Настройки дизайна',self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"themsrecaptcha",
                                              "label"=>__("Вид reCAPTCHA", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'white',
                                              "option"=>array('red'        => __('Красный',self::SLUG),
                                                              'white'      => __('Белый',self::SLUG),
                                                              'blackglass' => __('Черный',self::SLUG),
                                                              'clean'      => __('Прозрачный',self::SLUG),
                                                              'custom'     => __('Собственный дизайн',self::SLUG)),
                                              "desc"=>sprintf(__('Выберите дизайн reCAPTCHA подходящий Вашему сайту. Так же вы можете создать собственный дизайн. Более подробно о дизайне reCAPTCHA читайте %s здесь %s.', self::SLUG),'<a href="http://avkproject.ru/useful-articles/design-captcha.html" target="_blank">','</a>')),
                                        array("type"=>"text",
                                              "id"=>"textinputrecaptcha",
                                              "label"=>__("Текст reCAPTCHA", self::SLUG),
                                              "std" => __('Введите текст картинки', self::SLUG),
                                              "desc"=> __('Введите текст, который будет отображаться в поле ввода reCAPTCHA.', self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"typebutton",
                                              "label"=>__("Тип кнопок", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'css',
                                              "option"=>array('css'      => __('CSS кнопки',self::SLUG),
                                                              'black'    => __('Черные кнопки',self::SLUG),
                                                              'blue'     => __('Синие кнопки',self::SLUG),
                                                              'green'    => __('Зеленые кнопки',self::SLUG),
                                                              'purple'   => __('Фиолетовые кнопки',self::SLUG),
                                                              'red'      => __('Красные кнопки',self::SLUG),
                                                              'customer' => __('Пользовательские кнопки',self::SLUG)),
                                              "desc"=>__('Выберите дизайн кнопок. Так же можете использовать кнопки с Вашим дизайном, для этого создайте 5-ть изображений с расширением PNG, дайте им название:<br />кнопка входа =  login_customer.png<br />выхода = logout_customer.png<br />добавить в корзину = paid_customer.png<br />скачать = download_customer.png<br />купить = buy_customer.png<br />Для придания CSS стилей используйте класс <b><i>avk_buttons_customer</i></b>', self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"displayhelp",
                                              "label"=>__("Подсказки", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'on',
                                              "option"=>array('on'  => __('Включить',self::SLUG),
                                                              'off' => __('Выключить',self::SLUG)),
                                              "desc"=>__('Включить/Выключить всплывающие подсказки при регистрации', self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"maylogo",
                                              "label"=>__("Логотип", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'off',
                                              "option"=>array('on'  => __('Использовать',self::SLUG),
                                                              'off' => __('Не использовать',self::SLUG)),
                                              "desc"=>__('Вы можете использовать свой логотип, который выводится вместе с формой регистрации или восстановления пароля, для этого включите данную опцию и вставьте в поле «Ссылка на логотип» ссылку, ведущую на сам логотип.', self::SLUG)),
                                        array("type"=>"text",
                                              "id"=>"maylogourl",
                                              "label"=>__("Ссылка на логотип", self::SLUG),
                                              "std" => '',
                                              "desc"=> __('Данная опция меняет стандартный логотип над регистрационной формой WordPress. Загрузите свой логотип при помощи стандартного WordPress загрузчика, скопируйте ссылку изображения и вставьте в это поле. Логотип должен быть размером 548*126.', self::SLUG)),
                                        
                                        array("type"=>"closefieldset"),
                                        
                                        array("type"=>"openfieldset", "title"=>__('Пользователи',self::SLUG)),
                                        array("type"=>"select",
                                              "id"=>"customerroleavkshop",
                                              "label"=>__("Роль ", self::SLUG),
                                              "class"=>"select",
                                              "std" => 'buyer',
                                              "option"=>array('buyer'      => __('Покупатель',self::SLUG),
                                                              'subscriber' => __('Подписчик',self::SLUG)),
                                              "desc"=>__('Выберите роль, которая присвоится пользователю при регистрации.<br /><b>Внимание:</b> пользователь с ролью <b>«Покупатель»</b> не сможет зайти в админ панель вашего сайта.', self::SLUG)),
                                                                                
                                        array("type"=>"closefieldset"),
                                        
                                           );
        $this->metaBoxValue = array(
                        "open"   => array("type"   => "openmeta"),
                        "enabel" => array("type"   => "select",
                                          "id"     => "enabel_product_avk",
                                          "class"  => "select",
                                          "label"  => __("Добавить продукт:", self::SLUG),
                                          "std"    => 'off',
                                          "option" => array('on'=>__('Добавить',self::SLUG),'off'=>__('Убрать',self::SLUG)),
                                          "desc"   => __("Опция добавляет/удаляет товар на странице превью товара.", self::SLUG)),
                          "name" => array("type"   => "text",
                                          "id"     => "name_product_avk",
                                          "class"  => "input vadate",
                                          "label"  => __("Название продукта:", self::SLUG),
                                          "std"    => '',
                                          "desc"   => __("Введите название продукта, которое будет отображаться на странице с превью товаром в виде заголовка.", self::SLUG)),
                          "desc" => array("type"   => "textarea",
                                          "id"     => "desc_product_avk",
                                          "class"  => "input vadate",
                                          "label"  => __("Описание продукта:", self::SLUG),
                                          "std"    => '',
                                          "desc"   => __("Введите описание продукта, которое будет отображаться на странице с превью товаром в виде DIV блока или в качестве значения атрибута TITLE.", self::SLUG)),
                          "type" => array("type"   => "select",
                                          "id"     => "type_product_avk",
                                          "class"  => "select",
                                          "label"  => __("Тип продукта:", self::SLUG),
                                          "std"    => 'paid',
                                          "option" => array('paid'=>__('Платный',self::SLUG),'free'=>__('Бесплатный',self::SLUG)),
                                          "desc"   => __("Выберите, к какой категории относится Ваш продукт.", self::SLUG)),
                         "price" => array("type"   => "text",
                                          "id"     => "price_product_avk",
                                          "class"  => "input vadate",
                                          "label"  => __("Цена продукта:", self::SLUG),
                                          "std"    => '',
                                          "desc"   => __("Введите цену продукта.", self::SLUG)),
                          "file" => array("type"   => "file",
                                          "id"     => "file_product_avk",
                                          "class"  => "input vadate inpnamefile",
                                          "std"    => __('Загрузить файл ...',self::SLUG),
                                          "label"  => __("Продукт:", self::SLUG),
                                          "desc"   => __("Нажмите на кнопку добавить/удалить, чтобы загрузить файл и закрепить его за этой записью или удалить, чтобы закрепить новый.", self::SLUG)),
                      "new_name" => array("type"   => "text",
                                          "id"     => "new_name_product_avk",
                                          "class"  => "input vadate",
                                          "label"  => __("Имя продукта:", self::SLUG),
                                          "std"    => '',
                                          "desc"   => __("Введите для продукта имя, которое будет присваиваться при скачивании файла.", self::SLUG)),
                "enabel_counter" => array("type"   => "select",
                                          "id"     => "enabel_counter_product_avk",
                                          "class"  => "select",
                                          "label"  => __("Отображение счетчика:", self::SLUG),
                                          "std"    => 'off',
                                          "option" => array('on'=>__('Показывать',self::SLUG),'off'=>__('Убрать',self::SLUG)),
                                          "desc"   => __("Опция показывает/скрывает счетчик загрузок на странице с превью товара.", self::SLUG)),
                       "counter" => array("type"   => "readonly",
                                          "id"     => "counter_product_avk",
                                          "class"  => "input readonlyavk",
                                          "label"  => __("Счетчик загрузок:", self::SLUG),
                                          "std"    => 0,
                                          "desc"   => __("Данная опция отображает количество скачиваний данного товара.", self::SLUG))
                                    );

        $this->submenuPage = array(
            20 => array('parent_slug'=>self::SLUG.'-settings',
                        'page_title'=>__('Настройка систем оплат',self::SLUG).' '.$this->name,
                        'menu_title'=>__('Системы оплаты',self::SLUG),
                        'capability'=>1,
                        'menu_slug'=>self::SLUG.'-table',
                        'function'=>array (&$this, 'get_page_menu')),
            40 => array('parent_slug'=>self::SLUG.'-settings',
                        'page_title'=>__('Инструменты для',self::SLUG).' '.$this->name,
                        'menu_title'=>__('Инструменты',self::SLUG),
                        'capability'=>1,
                        'menu_slug'=>self::SLUG.'-action-file',
                        'function'=>array (&$this, 'get_page_menu')),
        );
        
        $this->submenuBar = array(
            array( 'id' => self::SLUG . '-sub-0',  'title' => __('Главные настройки', self::SLUG), 'href'=> admin_url('admin.php?page=' . self::SLUG . '-settings'),'parent' => self::SLUG ),
            array( 'id' => self::SLUG . '-sub-10', 'title' => __('Клиенты и продажи', self::SLUG), 'href'=> admin_url('users.php?role=' . $this->actMainSettings['customerroleavkshop']), 'parent' => self::SLUG ),
            array( 'id' => self::SLUG . '-sub-20', 'title' => __('Системы оплаты', self::SLUG), 'href'=> admin_url('admin.php?page=' . self::SLUG . '-table&tab=interkassa'), 'parent' => self::SLUG ),
            array( 'id' => self::SLUG . '-sub-30', 'title' => __('Инструменты', self::SLUG), 'href'=> admin_url('admin.php?page=' . self::SLUG . '-action-file'), 'parent' => self::SLUG )
        );
        
        $this->queryShopping = "CREATE TABLE IF NOT EXISTS `" . DB_NAME . "`.`" . $wpdb->prefix . $this->actMainSettings['tableshopping'] . "`(
                                `id` INT NOT NULL AUTO_INCREMENT,
                                `id_post` INT NOT NULL,
                                `customer_id` INT NOT NULL,
                                `status_purchase` VARCHAR(10) NOT NULL DEFAULT 'in_hand',
                                `payment_system` VARCHAR(20),
                                `counter_downloads` INT NOT NULL DEFAULT 0,
                                `amount` INT NOT NULL DEFAULT 1,
                                `order_status` VARCHAR(10) NOT NULL DEFAULT 'activ',
                                `datetime` INT NOT NULL,
                                PRIMARY KEY (`id`))
                                ENGINE = InnoDB";
        $this->queryDownload = "CREATE TABLE IF NOT EXISTS `" . DB_NAME . "`.`" . $wpdb->prefix . $this->actMainSettings['tabledownload'] . "`(
                                `id` INT NOT NULL AUTO_INCREMENT,
                                `id_post` INT NOT NULL,
                                `customer_id` INT NOT NULL,
                                `counter_downloads` INT NOT NULL,
                                `type_goods` VARCHAR(10) NOT NULL,
                                `datetime` INT NOT NULL,
                                PRIMARY KEY (`id`))
                                ENGINE = InnoDB";
    }
}
?>