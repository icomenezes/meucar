<?php

//phpinfo();

echo "Se você esta vendo um numero abaixo, você resolveu o problema!<br>";

$mail = imap_open("{imap.gmail.com:993/imap/ssl}INBOX", "loja2select@gmail.com","contato2123");

echo " ". imap_num_msg($mail) . " ";


//var_export($mail);


if(isset($mail)){
        echo "<br><br><br>Obrigado";
}


?>

