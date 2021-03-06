<?php

class IndexController
{
    public function index()
    {
        $html = '
            <div class="container" style="margin-top:20px">

            <form id="data-form" role="form" method="post" action="' . core::$config['http_home'] . '/get">

            <div class="tab-content" id="cards-content">
                <div class="tab-pane active" id="home">' . $this->includeView('CardIndex') . '</div>
                <div class="tab-pane" id="data">' . $this->includeView('CardData') . '</div>
                <div class="tab-pane" id="software">' . $this->includeView('CardSoftware') . '</div>
                <div class="tab-pane" id="hosting">' . $this->includeView('CardHosting') . '</div>
                <div class="tab-pane" id="support">' . $this->includeView('CardSupport') . '</div>
                <div class="tab-pane" id="summary">' . $this->includeView('CardSummary') . '</div>
            </div>

            </form>

            </div>

            <div class="summ-panel">
                <div class="container">
                    <ul class="nav nav-pills" id="cards">
                        <li style="display: none" class="active"><a href="#home" data-toggle="pill">Начало</a></li>
                        <li><a href="#data" data-toggle="pill" class="checkable-button"><span class="glyphicon glyphicon-ok"></span> Геоданные</a></li>
                        <li><a href="#hosting" data-toggle="pill" class="checkable-button"><span class="glyphicon glyphicon-ok"></span> Веб</a></li>
                        <li><a href="#software" data-toggle="pill" class="checkable-button"><span class="glyphicon glyphicon-ok"></span> Софт</a></li>
                        <li><a href="#support" data-toggle="pill" class="checkable-button"><span class="glyphicon glyphicon-ok"></span> Поддержка</a></li>
                        <li style="float:right""><a href="#summary" data-toggle="pill"><span class="glyphicon glyphicon-shopping-cart"></span> Итого</a></li>
                    </ul>
                </div>
            </div>
        ';

        return include(dirname(__FILE__) . '/../View/Common.php');
    }

    public function get()
    {
        if (!Core::$user->isLogin()) {
            if (isset($_REQUEST['result'])) {
                    $_SESSION['savedResult'] = $_REQUEST['result'];
            }
            $_SESSION['authReturnUrl'] = core::$config['http_home'] . '/get';
            go(core::$config['http_home'] . '/auth');
        }

        if (isset($_SESSION['savedResult']) && !isset($_REQUEST['result'])) {
            $_REQUEST['result'] = $_SESSION['savedResult'];
        }

        function is_valid_domain_name($domain_name) {
            return (preg_match("/^([a-z\d](-*[a-z\d])*)(([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
                && preg_match("/^.{1,20}$/", $domain_name) //overall length check
                /*&& preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)*/   ); //length of each label
        }

        if (isset($_REQUEST['result']['hosting'][0])) {
            $row = $_REQUEST['result']['hosting'][0];
            $instance_id = $row['title'];
            if($instance_id != '') {
                $error = null;
                if(count(Core::$sql->get('id', DB . 'hosting', 'instance_id=' . Core::$sql->s($instance_id)))) {
                    $error = 'Такое имя "'. escape($instance_id) . '" уже существует';
                } else if(count(Core::$sql->get('id', DB . 'hosting', 'owner_id=' . Core::$sql->i(Core::$user->info['id'])))) {
                    $error = 'Может быть только один хостинг типа "Простой"';
                } else if(!is_valid_domain_name($instance_id)) {
                    $error = 'Имя "'. escape($instance_id) . '" не подходит. Максимальная длина 20 символов. Только латинские символы и арабские цифры';
                }

                if($error) {
                    $html = '
                    <div class="container" style="margin-top:20px">
                        <div class="alert alert-danger"><p>'. $error . '</p></div>
                    </div>
                ';
                    return include(dirname(__FILE__) . '/../View/Common.php');
                }
            }
        }
        Core::$sql->insert(array(
            'status_id' => Core::$sql->i(0),
            'insert_user_id' => Core::$sql->i(Core::$user->info['id']),
            'insert_stamp' => Core::$sql->i(Core::$time['current_time']),
        ), DB . 'order');

        $orderId = Core::$sql->get_last_id();

        // Data

        /*if (isset($_REQUEST['result']['data'][0])) {
            $row = $_REQUEST['result']['data'][0];
            if ($row['selected'] == 'true') {
                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 15,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');
            }
        }

        if (isset($_REQUEST['result']['data'][1])) {
            $row = $_REQUEST['result']['data'][1];
            if ($row['selected'] == 'true') {
                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 16,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');
            }
        }*/

        // Software

        /*if (isset($_REQUEST['result']['software'][0])) {
            $row = $_REQUEST['result']['software'][0];
            if ($row['selected'] == 'true') {
                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 20,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');
            }
        }

        if ($_REQUEST['result']['software'][1]) {
            $row = $_REQUEST['result']['software'][1];
            if ($row['selected'] == 'true') {
                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 21,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');
            }
        }

        if (isset($_REQUEST['result']['software'][2])) {
            $row = $_REQUEST['result']['software'][2];
            if ($row['selected'] == 'true') {
                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 22,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');
            }
        }*/

        // Hosting

        echo 123;
        if (isset($_REQUEST['result']['hosting'][0])) {
            $row = $_REQUEST['result']['hosting'][0];
            if ($row['selected'] == 'true') {
                $row['password'] = substr(sha1(sha1(mt_rand())), 0, 7);
                $row['title'] = $row['title'];


                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 17,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');

                Hosting::create(
                    $row['title'],
                    $row['password'],
                    Core::$user->info['id']
                );
                echo 12324;
                $template_vars = array(
                    '{site_url}' => Core::$config['site']['url'],
                    '{site_title}' => Core::$config['site']['title'],
                    '{site_email}' => Core::$config['site']['email'],
                    '{email}' => Core::$user->info['email'],
                    '{account_id}' => Core::$user->info['id'],
                    '{hosting_plan}' => 'Simple',
                    '{project_id}' => $row['title'],
                    '{order_id}' => $orderId,
                );

                $backendEmail = 'sim@gis-lab.info';

                $message = str_replace(array_keys($template_vars), array_values($template_vars),
                    s('
                    Order Id: {order_id}
                    Account Id: {account_id}
                    Email: {email}
                    Hosting plan: {hosting_plan}
                    Project Id: {project_id}
                    '));

                mail_send(core::$config['site']['email_title'], core::$config['site']['email'], $backendEmail,
                    s('Hosting request'), $message);

            }
        }

        // Support

        /*if (isset($_REQUEST['result']['support'][0])) {
            $row = $_REQUEST['result']['support'][0];
            die();
            if ($row['selected'] == 'true') {
                Core::$sql->insert(array(
                    'order_id' => Core::$sql->i($orderId),
                    'item_id' => 23,
                    'amount' => 1,
                    'price' => 0,
                    'details' => Core::$sql->s(serialize($row)),
                ), DB . 'order_item');
            }
        }*/
        go(core::$config['http_home'] . '/order/' . $orderId);
    }

    public function includeView($viewName) {
        $path = dirname(__FILE__) . '/../View/' . $viewName . '.php';
        if (file_exists($path)) {
            ob_start();
            include($path);
            return ob_get_clean();
        }
        return false;
    }

    public function get404()
    {
        header('HTTP/1.0 404 Not Found');

        $html = '<div class="container">'
            . '<h3>Запрашиваемая страница не найдена</h3>'
            . '<p>Вы можете продолжить просмотр с <a href="/">главной страницы</a></p>'
            . '</div>';

        return include(dirname(__FILE__) . '/../View/Common.php');
    }
}