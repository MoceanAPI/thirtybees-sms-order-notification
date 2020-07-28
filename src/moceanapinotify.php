<?php

if (!defined('_PS_VERSION_')) {
    exit;
}


class MoceanAPINotify extends Module
{
    
    const MOCEAN_API_NOTIFY_KEY = 'MOCEAN_API_NOTIFY_KEY';
    const MOCEAN_API_NOTIFY_SECRET = 'MOCEAN_API_NOTIFY_SECRET';
    const MOCEAN_API_FROM = 'MOCEAN_API_FROM';
    const MOCEAN_API_ADMIN_ENABLE_NOTIFY = 'MOCEAN_API_ADMIN_ENABLE_NOTIFY';
    const MOCEAN_API_ADMIN_PHONE = 'MOCEAN_API_ADMIN_PHONE';
    const MOCEAN_API_ADMIN_MESSAGE = 'MOCEAN_API_ADMIN_MESSAGE';
    const MOCEAN_API_DEFAULT_MESSAGE = 'MOCEAN_API_DEFAULT_MESSAGE';

    public function __construct()
    {
        $this->name             = 'moceanapinotify';
        $this->tab              = 'advertising_marketing';
        $this->version          = '1.0.1';
        $this->author           = 'Micro Ocean Technologies';
        $this->need_instance    = 1;
        $this->bootstrap        = true;
        $this->controller_name  = 'AdminMoceanAPINotify';
        parent::__construct();
        
        $this->displayName      = $this->l('Mocean SMS API Notification');
        $this->description      = $this->l('MoceanAPI Send SMS for ThirtyBees');
        $this->confirmUninstall = $this->l('This module will be removed from your store. Are you sure?');
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_,
            );
        $this->need_instance    = 1;

        $this->module_key       = '2b92f491370dbb5da3c7018b2c3f5925';

        $this->hooks            = array(
            'dashboardZoneTwo'
            );
    }
    
    
    public function install($createTables = true)
    {
        if (!parent::install()) {
            return false;
        }
    
        Configuration::updateGlobalValue(static::MOCEAN_API_NOTIFY_KEY, '');
        Configuration::updateGlobalValue(static::MOCEAN_API_NOTIFY_SECRET, '');
        Configuration::updateGlobalValue(static::MOCEAN_API_FROM, '');
        
        Configuration::updateGlobalValue(static::MOCEAN_API_ADMIN_ENABLE_NOTIFY, true);
        Configuration::updateGlobalValue(static::MOCEAN_API_ADMIN_PHONE, '');
        Configuration::updateGlobalValue(static::MOCEAN_API_ADMIN_MESSAGE, '');
        
        Configuration::updateGlobalValue(static::MOCEAN_API_DEFAULT_MESSAGE, '');

        if ($createTables) {
               \Db::getInstance()->execute(
                'CREATE TABLE `'._DB_PREFIX_.'moceansms_notify_log` (
                  `id` int(11) NOT NULL,
                  `sender` varchar(255) NOT NULL,
                  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `message` text NOT NULL,
                  `recipient` text NOT NULL,
                  `response` text NOT NULL,
                  `status` int(11) NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8'
            );
            
            \Db::getInstance()->execute("ALTER TABLE `"._DB_PREFIX_."moceansms_notify_log` ADD PRIMARY KEY(`id`);");
            \Db::getInstance()->execute("ALTER TABLE `"._DB_PREFIX_."moceansms_notify_log` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
        }
        $this->registerHooks();
        $this->installTab();
        return true;
    }

    public function registerHooks()
    {
        return (
            $this->registerHook('actionOrderStatusPostUpdate')
        );
    }

    public function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName($this->controller_name);
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = $this->controller_name;
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'MoceanAPI SMS History';
        }
        $tab->id_parent = (int) Tab::getIdFromClassName($this->controller_name);
        $tab->module = $this->name;

        return $tab->save();
    }


    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName($this->controller_name);
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }


    public function uninstall()
    {
        \Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'moceansms_notify_log`');
        return ($this->uninstallTab() && parent::uninstall());
    }

    public function getMoceanConfigFormHelper()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fieldsForm['config']['form'] = [
            'legend' => [
                'title' => $this->l('Mocean Configuration'),
                'icon'  => 'icon-cogs',
            ],
            'input'  => [
                [
                    'type'     => 'text',
                    'label'    => $this->l('Mocean Key'),
                    'name'     => static::MOCEAN_API_NOTIFY_KEY,
                    'size'     => 70,
                    'required' => true,
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Mocean Secret'),
                    'name'     => static::MOCEAN_API_NOTIFY_SECRET,
                    'size' => 70,
                    'required' => true,
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Mocean From'),
                    'name'     => static::MOCEAN_API_FROM,
                    'size'     => 70,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang'    => $lang['id_lang'],
                'iso_code'   => $lang['iso_code'],
                'name'       => $lang['name'],
                'is_default' => ($defaultLang == $lang['id_lang'] ? 1 : 0),
            ];
        }

        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'token='.Tools::getAdminTokenLite('AdminModules'),
            ],
        ];

        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name.'config';

        $helper->fields_value = $this->getFormValues();

        return $helper;
    }
    
    public function getMoceanAdminFormHelper()
    {
        unset($this->fieldsForm);
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fieldsForm['admin']['form'] = [
            'legend' => [
                'title' => $this->l('Mocean Admin Notification'),
                'icon'  => 'icon-user',
            ],
            'input'  => [
                [
                    'type'     => 'switch',
                    'label'    => $this->l('Enable Admin Notification'),
                    'name'     => static::MOCEAN_API_ADMIN_ENABLE_NOTIFY,
                    'required' => false,
                    'class'    => 't',
                    'is_bool'  => true,
                    'values'   => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ],
                [
                    'type'     => 'text',
                    'label'    => $this->l('Phone number'),
                    'name'     => static::MOCEAN_API_ADMIN_PHONE,
                    'size' => 70,
                    'required' => true,
                ],
                [
                    'type'     => 'textarea',
                    'label'    => $this->l('Admin Message'),
                    'name'     => static::MOCEAN_API_ADMIN_MESSAGE,
                    'rows'     => 7,
                    'cols'     => 66,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang'    => $lang['id_lang'],
                'iso_code'   => $lang['iso_code'],
                'name'       => $lang['name'],
                'is_default' => ($defaultLang == $lang['id_lang'] ? 1 : 0),
            ];
        }

        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'token='.Tools::getAdminTokenLite('AdminModules'),
            ],
        ];

        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name.'admin';

        $helper->fields_value = $this->getFormValues();

        return $helper;
    }
    
    
    protected function getMoceanCustomersFormHelper($orders) {
        unset($this->fieldsForm);
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->fieldsForm[0]['form'] = [
        'legend' => [
            'title' => $this->l('Mocean Admin Notification'),
            'icon'  => 'icon-user',
        ]
        ];
        foreach($orders as $order) {
             $this->fieldsForm[0]['form']['input'][] = [
                    'type'     => 'switch',
                    'label'    => $this->l($order['name']),
                    'name'     => 'MOCEAN_API_STATUS_'.$order['id_order_state'],
                    'required' => false,
                    'class'    => 't',
                    'is_bool'  => true,
                    'values'   => [
                        [
                            'id'    => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled'),
                        ],
                        [
                            'id'    => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled'),
                        ],
                    ],
                ];
        }
        foreach($orders as $order) {
             $this->fieldsForm[0]['form']['input'][] = [
                'type'     => 'textarea',
                'label'    => $this->l('Message from '.$order['name']),
                'name'     => 'MOCEAN_API_MESSAGE_'.$order['id_order_state'],
                'rows'     => 7,
                'cols'     => 66,
                'required' => true,
            ];
        }
        $this->fieldsForm[0]['form']['submit'] = [
            'title' => $this->l('Save'),
        ];
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang'    => $lang['id_lang'],
                'iso_code'   => $lang['iso_code'],
                'name'       => $lang['name'],
                'is_default' => ($defaultLang == $lang['id_lang'] ? 1 : 0),
            ];
        }

        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'token='.Tools::getAdminTokenLite('AdminModules'),
            ],
        ];

        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name.'customers';

        $helper->fields_value = $this->getFormValues();
        return $helper;
    }
    
    protected function getFormValues()
    {
        $vars = [
                static::MOCEAN_API_NOTIFY_KEY,
                static::MOCEAN_API_NOTIFY_SECRET,
                static::MOCEAN_API_FROM,
                static::MOCEAN_API_ADMIN_ENABLE_NOTIFY,
                static::MOCEAN_API_ADMIN_PHONE,
                static::MOCEAN_API_ADMIN_MESSAGE,
        ];
        $orders = OrderStateCore::getOrderStates(1);
        foreach($orders as $order) {
            $vars[] = 'MOCEAN_API_STATUS_'.$order['id_order_state'];
            $vars[] = 'MOCEAN_API_MESSAGE_'.$order['id_order_state'];
        }
        return Configuration::getMultiple(
            $vars
        );
    }

    public function getContent()
    {
        $post_result = $this->postProcess();
        if(!isset($post_result['tab'])) {
            $post_result['tab'] = 'config';
        }
        $mocean_config = $this->getMoceanConfigFormHelper();
        $mocean_config_form = $mocean_config->generateForm($this->fieldsForm);

        $mocean_admin = $this->getMoceanAdminFormHelper();
        $mocean_admin_form = $mocean_admin->generateForm($this->fieldsForm);
        
        $orders = OrderStateCore::getOrderStates(1);
        $mocean_customers = $this->getMoceanCustomersFormHelper($orders);
        $mocean_customers_form = $mocean_customers->generateForm($this->fieldsForm);
        $mocean_key = Configuration::get(static::MOCEAN_API_NOTIFY_KEY);
        $mocean_secret = Configuration::get(static::MOCEAN_API_NOTIFY_SECRET);
        $balance = json_decode($this->getBalance($mocean_key, $mocean_secret));
        $this->loadAsset();
        $this->context->controller->addJS(dirname(__FILE__) . 'views/js/dashbypayment.js');
        $this->context->smarty->assign(
            array(
                'moduleName'    => $this->displayName,
                'moduleVersion' => $this->version,
                'moceanConfig' => $mocean_config_form,
                'moceanAdmin' => $mocean_admin_form,
                'moceanCustomer' => $mocean_customers_form,
                'post_result' => $post_result,
                'balance' => $balance,
            )
        );


        return $this->display(__FILE__, 'views/templates/admin/configuration.tpl');
    }

	 protected function postProcess()
     {
        $output = '';

        if (Tools::isSubmit('submit'.$this->name.'config')) {
            Configuration::updateValue(static::MOCEAN_API_NOTIFY_KEY, Tools::getValue(static::MOCEAN_API_NOTIFY_KEY));
            Configuration::updateValue(static::MOCEAN_API_NOTIFY_SECRET, Tools::getValue(static::MOCEAN_API_NOTIFY_SECRET));
            Configuration::updateValue(static::MOCEAN_API_FROM, Tools::getValue(static::MOCEAN_API_FROM));
            return ['success' => 'You have successfully made the changes config!', 'tab' => 'config'];
        }

        if (Tools::isSubmit('submit'.$this->name.'admin')) {
            Configuration::updateValue(static::MOCEAN_API_ADMIN_ENABLE_NOTIFY, Tools::getValue(static::MOCEAN_API_ADMIN_ENABLE_NOTIFY));
            Configuration::updateValue(static::MOCEAN_API_ADMIN_PHONE, Tools::getValue(static::MOCEAN_API_ADMIN_PHONE));
            Configuration::updateValue(static::MOCEAN_API_ADMIN_MESSAGE, Tools::getValue(static::MOCEAN_API_ADMIN_MESSAGE));
            return ['success' => 'You have successfully made the changes!', 'tab' => 'admin'];
        }
        
        if(Tools::isSubmit('submit'.$this->name.'customers')) {
            $orders = OrderStateCore::getOrderStates(1);
            foreach($orders as $order) {
                 Configuration::updateValue('MOCEAN_API_STATUS_'.$order['id_order_state'], Tools::getValue('MOCEAN_API_STATUS_'.$order['id_order_state']));
                 Configuration::updateValue('MOCEAN_API_MESSAGE_'.$order['id_order_state'], Tools::getValue('MOCEAN_API_MESSAGE_'.$order['id_order_state']));
            }
            return ['success' => 'You have successfully made the changes!', 'tab' => 'users'];
        }

        return $output;
    }
    
    public function hookactionOrderStatusPostUpdate($params) {
        $controller = Tools::getValue('controller');
        
        $order_status = $params['newOrderStatus'];
        $order_id = $params['id_order'];
        $order = new Order($order_id);

        $this->order['order_id'] = $order_id;
        $this->order['order_amount'] = count($order->getProducts());
        $this->order['order_status'] = $order_status->name;
        $this->order['order_product'] = $order->getProducts();
        
        $customer = new Customer ($order->id_customer);
        $this->billing['payment_method'] = $order->payment;
        $var_id_address = $order->id_address_invoice;
        $address = new Address($var_id_address);
        $this->billing['payment_method'] = $order->payment;
       
        $this->billing['billing_first_name'] = $customer->firstname;
        $this->billing['billing_last_name'] = $customer->lastname;

        $this->billing['billing_phone'] = (!empty($address->phone)) ? $address->phone:$address->phone_mobile;
        $this->billing['billing_email'] = $customer->email;
        $this->billing['billing_company'] = $address->company;
        $this->billing['billing_address'] = $address->address1;
        $this->billing['billing_country'] = $address->country;
        $this->billing['billing_city'] = $address->city;
        $this->billing['billing_postcode'] = $address->postcode;

        $mocean_key = Configuration::get(static::MOCEAN_API_NOTIFY_KEY);
        $mocean_secret = Configuration::get(static::MOCEAN_API_NOTIFY_SECRET);
        $mocean_from  = Configuration::get(static::MOCEAN_API_FROM);
        
        
        if($controller == 'AdminOrders') {
            $check_notify_status = Configuration::get('MOCEAN_API_STATUS_'.$order_status->id);
            if($check_notify_status == 1) {
                $message_notify = Configuration::get('MOCEAN_API_MESSAGE_'.$order_status->id);
                $message_notify = $this->replaceSpecialChars($message_notify, $this->specialChar());
                $phone = $this->billing['billing_phone'];
                $result_mocean = $this->sendMoceanSMS($mocean_key, $mocean_secret, $mocean_from, $message_notify, $phone);
                $response = addslashes($result_mocean);
                $status_raw = json_decode($result_mocean);
                $status = $status_raw->status;
                $date = date("Y-m-d H:i:s");
                \Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."moceansms_notify_log (`sender`, `date`, `message`, `recipient`, `response`, `status`) VALUES('$mocean_from', '$date', '$message_notify', '$phone', '$response' ,'$status')");
            }
        } else {
            $admin_notify = Configuration::get(static::MOCEAN_API_ADMIN_ENABLE_NOTIFY);
            if($admin_notify == 1) {
                $admin_message = Configuration::get(static::MOCEAN_API_ADMIN_MESSAGE);
                $admin_message = $this->replaceSpecialChars($admin_message, $this->specialChar());
                
                $admin_phone = Configuration::get(static::MOCEAN_API_ADMIN_PHONE);
                $result_mocean = $this->sendMoceanSMS($mocean_key, $mocean_secret, $mocean_from, $admin_message, $admin_phone); 
                $response = addslashes($result_mocean);
                $status_raw = json_decode($result_mocean);
                $status = $status_raw->messages[0]->status;
                $date = date("Y-m-d H:i:s");
                \Db::getInstance()->execute("INSERT INTO "._DB_PREFIX_."moceansms_notify_log (`sender`, `date`, `message`, `recipient`, `response`, `status`) VALUES('$mocean_from', '$date', '$admin_message', '$admin_phone', '$response' ,'$status')");
            }
        }
    }
    
    public function replaceSpecialChars($message, $special_chars) {
        foreach($special_chars as $key=>$value) {
            $message = str_replace("[$key]", $value, $message);
        }
        return $message;
    }
    
    public function specialChar() {

        $product_names = [];
        foreach( $this->order['order_product'] as $product) {
            $product_names[] = $product['product_name'];
        }
        $chars = [
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'shop_email' => Configuration::get('PS_SHOP_EMAIL'),
            'shop_url' => Configuration::get('PS_SHOP_DOMAIN_SSL'),

            'order_id' => $this->order['order_id'],
            'order_amount' => $this->order['order_amount'],
            'order_status' => $this->order['order_status'],
            'order_product' => implode(' | ',$product_names),

            'payment_method' => $this->billing['payment_method'],
            'billing_first_name' => $this->billing['billing_first_name'],
            'billing_last_name' => $this->billing['billing_last_name'],
            'billing_phone' => $this->billing['billing_phone'],
            'billing_email' => $this->billing['billing_email'],
            'billing_company' => $this->billing['billing_company'],
            'billing_address' => $this->billing['billing_address'],
            'billing_country' => $this->billing['billing_country'],
            'billing_city' => $this->billing['billing_city'],
            'billing_postcode' => $this->billing['billing_postcode'],
        ];

        return $chars;

    }
    
    protected function getBalance($mocean_key, $mocean_secret) {
    $url = 'https://rest.moceanapi.com/rest/2/account/balance?';
	$fields = array(
			'mocean-api-key' => $mocean_key,
			'mocean-api-secret' => $mocean_secret,
			'mocean-resp-format' => 'json',
	);
    
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');
    //return $url.$fields_string;
	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url.$fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//execute post
	$result = curl_exec($ch);
	//close connection

	return  $result;
    }
    
    
    protected function sendMoceanSMS($mocean_key, $mocean_secret, $mocean_from = "New Order", $mocean_message, $mocean_phone) {
	$url = 'https://rest.moceanapi.com/rest/2/sms';
	$fields = array(
			'mocean-api-key' => $mocean_key,
			'mocean-api-secret' => $mocean_secret,
			'mocean-from' => $mocean_from,
			'mocean-text' => $mocean_message,
			'mocean-to' => $mocean_phone,
			'mocean-resp-format' => 'json',
	);

	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL, $url);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	//execute post
	$result = curl_exec($ch);
	//close connection

	return  $result;
    }
    
    public function loadAsset()
    {
        // Load JS
        $jss = $this->_path . 'views/js/' . $this->name . '.js';

        $this->context->controller->addJS($jss);

        // Clean memory
        unset($jss);
    }
    
}
