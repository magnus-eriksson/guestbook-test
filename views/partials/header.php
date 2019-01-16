<!DOCTYPE html>
<html>
<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700,700i" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/static/main.css" />
</head>
<body>

    <header id="header">
        <div class="inner">

            <div id="logo">
                <a href="/">Guestbook</a>
            </div>

            <div id="user">

                <?php if ($user) : ?>

                    <?= $this->e($user['name']) ?> <a href="/logout" id="logout-btn">Log out</a>

                <?php else: ?>

                    <a href="/login">Log in</a> / <a href="/register">Register</a>

                <?php endif ?>

            </div>

        </div>
    </header>

    <div id="wrapper">
