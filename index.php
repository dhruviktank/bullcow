<?php
session_start();
if (isset($_POST["start"])) {
    $digits = range(0, 9);
    shuffle($digits);
    $randomNumber = $digits[0] * 1000 + $digits[1] * 100 + $digits[2] * 10 + $digits[3];
    $_SESSION["number"] = $randomNumber;
    $_SESSION["status"] = "start";
    $_SESSION["past"] = [];
    $_SESSION["count"] = 10;
    header("Location: ./");
}
if (isset($_POST["home"]) || isset($_POST["endgame"])) {
    foreach ($_SESSION as $key => $value) {
        unset($_SESSION[$key]);
    }
    header("Location: ./");
}
function getSessionData($key)
{
    if (isset($_SESSION[$key])) {
        return $_SESSION[$key];
    } else {
        return '';
    }
}
if (isset($_POST["guess"])) {
    if (getSessionData("count") == 0) {
        $_SESSION["result"] = "loose";
        $_SESSION["status"] = "end";
    }
    $_SESSION["guess"] = [];
    $_SESSION["guess"]["num"] = $_POST["guess"];
    if (getSessionData("guess")["num"] == $_SESSION["number"]) {
        $_SESSION["status"] = "end";
        $_SESSION["result"] = "win";
    } else {
        $number = (string) $_SESSION["number"];
        $guess = (string) getSessionData("guess")["num"];
        $bull = 0;
        $cow = 0;
        for ($i = 0; $i < 4; $i++) {
            if ($number[$i] === $guess[$i]) {
                $bull++;
                continue;
            } elseif (str_contains($number, $guess[$i])) {
                $cow++;
                continue;
            }
        }
        $_SESSION["guess"]["bull"] = $bull;
        $_SESSION["guess"]["cow"] = $cow;
        $_SESSION["past"][] = $_SESSION["guess"];
    }
    $_SESSION["count"]--;
    header("Location: ./");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php if (getSessionData("status") != "start" && getSessionData("status") != "end"): ?>
        <div id="before-start">
            <form action="" id="start" method="post">
                <button name="start" onclick="start()" type="submit">START</button>
            </form>
        </div>
    <?php endif; ?>
    <?php if (getSessionData("status") == "start"): ?>
        <div class="game">
            <p>Remaining chance:
                <?php echo getSessionData("count") ?>
            </p>
            <form action="" method="post">
                <input type="tel" min="0" placeholder="enter number to guess" max="9999" name="guess" id="guess" />
                <button type="submit" onclick="guess()">Guess</button>

                <?php if (getSessionData("guess") != "" && isset(getSessionData("guess")["bull"])): ?>
                    <p>
                        <?php echo getSessionData("guess")["bull"] . " Bull " . getSessionData("guess")["cow"] . " Cow "; ?>
                    </p>
                <?php endif; ?>
                <?php if (isset($_SESSION["past"])): ?>
                    <?php foreach (getSessionData("past") as $_guess): ?>
                        <p>
                            <?php echo $_guess["num"] . " --- " . $_guess["bull"] . " Bull " . $_guess["cow"] . " Cow"; ?>
                        </p>
                    <?php endforeach; ?>

                <?php endif; ?>
            </form>
            <p>
            <form action="" method="post"><button name="endgame" type="submit">End the game</button></form>
            </p>
        </div>
    <?php endif; ?>
    <?php if (getSessionData("status") == "end"): ?>
        <?php if (getSessionData("result") == "win"): ?>
            <p>Great You Winner</p>
            <form action="" method="post"><button name="home" type="submit">Home</button></form>
        <?php endif; ?>
        <?php if (getSessionData("result") == "loose"): ?>
            <p>You Loose....</p>
            <form action="" method="post"><button name="home" type="submit">Home</button></form>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>