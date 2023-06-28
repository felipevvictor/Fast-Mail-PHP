<?php

  require "../bibliotecas/PHPMailer/Exception.php";
  require "../bibliotecas/PHPMailer/OAuth.php";
  require "../bibliotecas/PHPMailer/PHPMailer.php";
  require "../bibliotecas/PHPMailer/POP3.php";
  require "../bibliotecas/PHPMailer/SMTP.php";

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;


    //print_r($_POST);

     class Mensagem{

            private $email = null;
            private $assunto = null;
            private $mensagem = null;
            public $status = array('codigo_status' => null, 'descricao_status' => null);

            public function __get($attr){
                return $this->$attr;
            }

            public function __set($attr, $valor){
                $this->$attr = $valor;
            }

            public function mensagemValida(){
                if(empty($this->email) || empty($this->assunto) || empty($this->mensagem)){
                    return false;
                }

                return true;
            }

        }

     $mensagem = new Mensagem();

     $mensagem->__set('email', $_POST['email']);
     $mensagem->__set('assunto', $_POST['assunto']);
     $mensagem->__set('mensagem', $_POST['mensagem']);

    //print_r($mensagem);

    if(!$mensagem->mensagemValida()){
        header('Location: app_fast_mail.html?redirected=1'); 
        echo 'Mensagem invalida';

    }

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = false;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'EmailDeEnvio';                     //SMTP username - UTILIZE UM EMAIL VALIDO
        $mail->Password   = 'SenhaCriptografada';                               //SMTP password - NECESSARIO CONFIGURAR GMAIL PARA OBTER SENHA
        $mail->SMTPSecure = 'tls';            //Enable implicit TLS encryption
        $mail->Port       = 587;                                   //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` PORTA PADRÂO GMAIL

        //Recipients
        $mail->setFrom('EmailDeEnvio', 'Teste');
        $mail->addAddress($mensagem->__get('email'));     //Add a recipient
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
        $mail->AltBody = 'É necessário utilizar um client que suporte o HTML para ter o acesso total dessa mensagem';

        $mail->send();
        
        $mensagem->status['codigo_status'] = 1;
        $mensagem->status['descricao_status'] = 'Email enviado com Sucesso';

    } catch (Exception $e) {

        $mensagem->status['codigo_status'] = 2;
        $mensagem->status['descricao_status'] = 'Não foi possível enviar este email, tente novamente. DETALHES DO ERRO: ' . $mail->ErrorInfo;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php if($mensagem->status['codigo_status'] == 1) { ?>

                    <div class="container">
                        <h1 class="display-4 text-success">Sucesso</h1>
                        <p><?= $mensagem->status['descricao_status'] ?></p>
                        <a href="app_fast_mail.html" class="btn btn-primary btn-lg mt-5">Voltar</a>
                    </div>
               <?php } ?>

               <?php if($mensagem->status['codigo_status'] == 2) { ?>

                    <div class="container">
                        <h1 class="display-4 text-danger">Erro</h1>
                        <p><?= $mensagem->status['descricao_status'] ?></p>
                        <a href="app_fast_mail.html" class="btn btn-danger btn-lg mt-5">Voltar</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>
