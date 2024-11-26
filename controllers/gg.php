<?php
 
use Postmark\PostmarkClient;
 
class Email
{
    public $code;
    public $email;
 
    public function __construct($code, $email){
        $code = $this->code;
        $email =  $this->email;
    }
 
    public function codAuto()
    {
        $client = new PostmarkClient("22ed804a-3ad8-4752-914c-225acb0c5c26");
 
        try {
            $fromEmail = "forgotpass@pulpafruit.com";
            $toEmail = "auxdesarrollo@pulpafruit.com";
            $subject = "Autorización cierre de caja con observación";
            $htmlBody = '<b>¡Código de Autorización!</b>
            <br>
               <p>Por favor, Envie este código al usuario que esta intentando cerrar caja para que finalice el proceso de manera exitosa. <br>
                Recuerda que este código de un solo uso y es único para el proceso que lo está solicitando.</p>
               <br><br>
               <p>Este es el código para ingresar al sistema: <strong>' . $code . '</strong></p>
               <br><br/>Cordialmente,
               <br/><img src="./vistas/img/multimedia/Juan Perez.jpg" alt="Firma Correo"/>';
            $textBody = '¡Código de Autorización!';
            $tag = "analistabi@pulpafruit.com";
            $trackOpens = true;
            $trackLinks = "None";
            $messageStream = "outbound";
 
            // Send an email:
            $sendResult = $client->sendEmail(
                $fromEmail,
                $toEmail,
                $subject,
                $htmlBody,
                $textBody,
                $tag,
                $trackOpens,
                NULL, // Reply To
                NULL, // CC
                NULL, // BCC
                NULL, // Header array
                NULL, // Attachment array
                $trackLinks,
                NULL, // Metadata array
                $messageStream
            );
 
            if ($sendResult) {
                // guardamos token en la db
                $respuesta = ModeloAdministrador::mdlActualizarCode2fa($tabla, $data);
                if ($respuesta == "ok") {
                    //$_SESSION["validarSesionBackend"] = "2fa";
                    echo '<script>
                            window.location = "form2fa";
                          </script>';
                } else if ($respuesta == "error") {
                    echo '<br>
                          <div class="alert alert-danger">El código no pudo ser registrado en la base de datos. Por favor, comuniquese con el area de sistemas</div>';
                }
            }
            // echo '¡Mensaje enviado!';
        } catch (Exception $e) {
            //echo $e->getMessage();
            echo $sendResult;
            return "Ocurrio un error: $sendResult Informar al area de sistemas, Disculpe los inconvenientes.";
        }
    }
}