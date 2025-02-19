<?php use Yii;?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: "Poppins", Helvetica, "sans-serif";
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #17bebb;
            text-align: center;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            color: #17bebb;
            text-align: center;
            margin: 20px 0;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            text-align: justify;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>UNDANGAN DIGITAL PORTAL PINTAR</h1>
        <h3><?= Yii::$app->params['namaSatker']?></h3>
        <p><?= $message ?></p>
        <div class="footer">
            &copy; <?= date('Y') ?> Portal Pintar. All rights reserved.
        </div>
    </div>
</body>
</html>
