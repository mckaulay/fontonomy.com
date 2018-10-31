<?php
require_once './vendor/autoload.php';

$helperLoader = new SplClassLoader('Helpers', './vendor');
$mailLoader   = new SplClassLoader('SimpleMail', './vendor');

$helperLoader->register();
$mailLoader->register();

use Helpers\Config;
use SimpleMail\SimpleMail;

$config = new Config;
$config->load('./config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = stripslashes(trim($_POST['form-name']));
    $email   = stripslashes(trim($_POST['form-email']));
    $subject = stripslashes(trim($_POST['form-subject']));
    $message = stripslashes(trim($_POST['form-message']));
    $pattern = '/[\r\n]|Content-Type:|Bcc:|Cc:/i';

    if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $subject)) {
        die("Header injection detected");
    }

    $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($name && $email && $emailIsValid && $subject && $message) {
        $mail = new SimpleMail();

        $mail->setTo($config->get('emails.to'));
        $mail->setFrom($config->get('emails.from'));
        $mail->setSender($name);
        $mail->setSenderEmail($email);
        $mail->setSubject($config->get('subject.prefix') . ' ' . $subject);

        $body = "
        <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html>
            <head>
                <meta charset=\"utf-8\">
            </head>
            <body>
                <h1>{$subject}</h1>
                <p><strong>{$config->get('fields.name')}:</strong> {$name}</p>
                <p><strong>{$config->get('fields.email')}:</strong> {$email}</p>
                <p><strong>{$config->get('fields.message')}:</strong> {$message}</p>
            </body>
        </html>";

        $mail->setHtml($body);
        $mail->send();

        $emailSent = true;
    } else {
        $hasError = true;
    }
}
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/css/bootstrap.min.css" integrity="sha384-AysaV+vQoT3kOAXZkl02PThvDr8HYKPZhNT5h/CXfBThSRXQ6jW5DO2ekP5ViFdi" crossorigin="anonymous">
        <link rel="stylesheet" href="http://www.fontonomy.com/css/style.css">
        <script type="text/javascript" src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.5/js/bootstrap.min.js" integrity="sha384-BLiI7JTZm+JWlgKa0M0kGRpJbF2J8q+qreVrKBC47e3K6BW78kGLrCkeRX6I9RoK" crossorigin="anonymous"></script>
        <link href="https://fonts.googleapis.com/css?family=Rosario:400,400i,700,700i" rel="stylesheet">

        <link rel="shortcut icon" href="http://www.fontonomy.com/images/favicon.png" />

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

        <title>Contact Us - Fontonomy</title>
    </head>

    <body>
        <nav class="navbar navbar-full navbar-dark navbar-red">
            <div class="container"><a class="navbar-brand" href="http://www.fontonomy.com/">fontonomy</a>
                <ul class="nav navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="http://www.fontonomy.com/fonts/" id="supportedContentDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fonts</a>
                        <div class="dropdown-menu" aria-labelledby="supportedContentDropdown">
                            <a class="dropdown-item" href="http://www.fontonomy.com/fonts/sans-serif/">Sans-Serif</a>
                            <a class="dropdown-item" href="http://www.fontonomy.com/fonts/serif/">Serif</a>
                            <a class="dropdown-item" href="http://www.fontonomy.com/fonts/monospace/">Monospace</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://www.fontonomy.com/tips/">Tips</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://www.fontonomy.com/#About">About</a>
                    </li>
                    <!--<li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>-->
                </ul>
            </div>
        </nav>
        <ol class="breadcrumb">
            <div class="container">
                <li class="breadcrumb-item"><a href="http://www.fontonomy.com/">Home</a></li>
                <li class="breadcrumb-item active">Contact Us</li>
            </div>
        </ol>
        <div class="container">
            <div class="row mt-3">
                <div class="col-md-9" style="font-size: 80%;">
                    <h1>Contact Us</h1>
                    <?php if(!empty($emailSent)): ?>
                        <div class="">
                            <div class="alert alert-success text-center">
                                <?php echo $config->get('messages.success'); ?>
                            </div>
                        </div>
                        <?php else: ?>
                            <?php if(!empty($hasError)): ?>
                                <div class="">
                                    <div class="alert alert-danger text-center">
                                        <?php echo $config->get('messages.error'); ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                    <div class="row">
                                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="application/x-www-form-urlencoded" id="contact-form" class="form-horizontal col-md-9" method="post">
                                            <div class="form-group row">
                                                <label for="form-name" class="col-lg-2 control-label">
                                                    <?php echo $config->get('fields.name'); ?>
                                                </label>
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control" id="form-name" name="form-name" placeholder="<?php echo $config->get('fields.name'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="form-email" class="col-lg-2 control-label">
                                                    <?php echo $config->get('fields.email'); ?>
                                                </label>
                                                <div class="col-lg-10">
                                                    <input type="email" class="form-control" id="form-email" name="form-email" placeholder="<?php echo $config->get('fields.email'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="form-subject" class="col-lg-2 control-label">
                                                    <?php echo $config->get('fields.subject'); ?>
                                                </label>
                                                <div class="col-lg-10">
                                                    <input type="text" class="form-control" id="form-subject" name="form-subject" placeholder="<?php echo $config->get('fields.subject'); ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="form-message" class="col-lg-2 control-label">
                                                    <?php echo $config->get('fields.message'); ?>
                                                </label>
                                                <div class="col-lg-10">
                                                    <textarea class="form-control" rows="3" id="form-message" name="form-message" placeholder="<?php echo $config->get('fields.message'); ?>" required></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-lg-2 col-lg-10">
                                                    <button type="submit" class="btn btn-primary">
                                                        <?php echo $config->get('fields.btn-send'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <?php endif; ?>

                                        <script type="text/javascript" src="public/js/contact-form.js"></script>
                                        <script type="text/javascript">
                                            new ContactForm('#contact-form');

                                        </script>

                </div>
                <div id="side-bar" class="col-md-3">
                    <div class="mb-2">
                        <!--<img src="http://placehold.it/300x250?text=Advertisement">--></div>
                    <h2>Web Font Categories</h2>
                    <ul class="list-unstyled">
                        <li><a href="http://www.fontonomy.com/fonts/sans-serif/">Sans-serif</a></li>
                        <li><a href="http://www.fontonomy.com/fonts/serif/">Serif</a></li>
                        <li><a href="http://www.fontonomy.com/fonts/monospace/">Monospace</a></li>
                    </ul>
                    <!--<h2>Web Font Uses</h2>
                <ul class="list-unstyled">
                    <li><a href="#">Headings</a></li>
                    <li><a href="#">Paragraphs</a></li>
                    <li><a href="#">Display</a></li>
                </ul>
                <img src="http://placehold.it/300x250?text=Advertisement" class="mb-1">
                <img src="http://placehold.it/300x250?text=Advertisement" class="mb-1">
                <img src="http://placehold.it/300x250?text=Advertisement">-->
                </div>
            </div>



        </div>
        <nav class="navbar footer navbar-full navbar-dark navbar-red mt-2">
            <div class="container"><a class="navbar-brand" href="http://www.fontonomy.com/">fontonomy</a><span class="navbar-text invert" style="color: #fff;">© 2016 McKaulay Kolakowski</span>
                <ul class="nav navbar-nav float-xs-right">
                    <li class="nav-item">
                        <a class="nav-link active" href="http://www.fontonomy.com/contact-us/">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://www.fontonomy.com/privacy/">Privacy</a>
                    </li>
                </ul>
            </div>
        </nav>
        <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-89256154-1', 'auto');
            ga('send', 'pageview');

        </script>
    </body>

    </html>
