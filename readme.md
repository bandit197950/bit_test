1) На базе выполнить скрипт create.sql . Он создаст пользователя super_admin и базу bit_test с 2-мя таблицами:
users, balance_history.
users - таблица пользователей, содержит 1-го пользователя с начисленным балансом.
balance_history - история списаний.
2) www/conf/config.php настроить Config::conf['db_host'] и Config::$conf['doc_path'], если потребуется.
3) Транзакции с флагом MYSQLI_TRANS_START_READ_ONLY не поддерживается на установленом mysql на моей машине (версия меньше 5.6.5), поэтому 
в зависимости  от версии, транзакции работает несколько по иному.
4) Тестирование выполнялось в программном окружении:
   windows7, xampp (apache + mysql 5.6.34-79.1 + php 5.6.3)
5) Логин, пароль:
test@example.com
test
