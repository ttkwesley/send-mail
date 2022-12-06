<?php

require "./biblioteca/PHPmailer/Exception.php";
require "./biblioteca/PHPmailer/OAuth.php";
require "./biblioteca/PHPmailer/PHPMailer.php";
require "./biblioteca/PHPmailer/POP3.php";
require "./biblioteca/PHPmailer/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mensagem
{
    //Atributos
    private $para = null;
    private $assunto = null;
    private $mensagem = null;
    public $status = ['codigo_status' => null, 'descricao_status' => ''];
    //Metodos 
    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }
    public function __mensagemValida()
    {
        //Validação de campo para verificar se os campos estão preenchidos 
        if (empty($this->para) || empty($this->assunto) || empty($this->mensagem)) {
            return false;
        } else {
            return true;
        }
    }
}

$mensagem = new Mensagem();
//Receber os valores do front end e setar no back end
$mensagem->__set('para', $_POST['email']);
$mensagem->__set('assunto', $_POST['assunto']);
$mensagem->__set('mensagem', $_POST['mensagem']);

if (!$mensagem->__mensagemValida()) {
    echo 'Mensagem não é valida';
    header('location: index.php?=AusenciaDeCampos');
}

//Instanciando a classe da lib 
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = false;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'protocolo smtp da plataforma desejada';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'email';                     //SMTP username
    $mail->Password   = 'senha do email';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('', ''); //Email e nome da pessoa que enviou 
    $mail->addAddress($mensagem->__get('para'));     //Add a recipient
    //$mail->addReplyTo('info@example.com', 'Information');
    //$mail->addCC('cc@example.com');
    //$mail->addBCC('bcc@example.com');

    //Attachments
    //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $mensagem->__get('assunto');
    $mail->Body    = $mensagem->__get('mensagem');
    $mail->AltBody = 'É necessario usar um client que suporte html para ter acesso total ao conteudo dessa pensagem';

    $mail->send();
    $mensagem->status['codigo_status'] = 1;
    $mensagem->status['descricao_status'] = 'E-mail enviado com sucesso';
} catch (Exception $e) {
    $mensagem->status['codigo_status'] = 2;
    $mensagem->status['descricao_status'] = 'Não foi possivel enviar esse email. Por favor, tente novamente mais tarde. Detalhes do erro: ' . $mail->ErrorInfo;
}

?>


<!-- Menu intuitivo dos botoes e mensagens de envio -->
<html>

<head>
    <meta charset="utf-8" />
    <title>Send Mail</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>

<body>
    <div class="container">
        <div class="py-3 text-center">
            <img class="d-block mx-auto mb-2" src="logo.png" alt="" width="72" height="72">
            <h2>Send Mail</h2>
            <p class="lead">Seu app de envio de e-mails particular!</p>
        </div>
        <div class="row">
            <div class="col-md-12">

                <?php
                if ($mensagem->status['codigo_status'] == 1) { ?>
                    <div class="container ">
                        <h1 class="display-4 text-success">Sucesso</h1>
                        <p><?php echo $mensagem->status['descricao_status'] ?></p>
                        <a href="index.php" class="btn btn-success btn-lg mt-5 text-white">Voltar</a>
                    </div>

                <?php } ?>
                <?php
                if ($mensagem->status['codigo_status'] == 2) { ?>
                    <div class="container">
                        <h1 class="display-4 text-danger">Ops...</h1>
                        <p><?php echo $mensagem->status['descricao_status'] ?></p>
                        <a href="index.php" class="btn btn-danger btn-lg mt-5 text-white">Voltar</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>

</html>