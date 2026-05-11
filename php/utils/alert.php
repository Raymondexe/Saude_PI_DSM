<?php

function meuAlerta($msg, $redirectURL = null, $msgUrl = null) {
    $linkHtml = '';

    if ($redirectURL) {
        $linkHtml = '
            <a href="' . htmlspecialchars($redirectURL) . '" style="
                margin-top: auto;
                padding: 3px 8px 3px 8px;
                background-color: lightblue;
                border: 2px solid blue;
                border-radius: 10px;
                text-decoration: none;
                color: blue;
            ">' . htmlspecialchars($msgUrl) . '</a>
        ';
    }

    echo '

         <!-- Backdrop: cobre a tela toda -->
        <div id="alert-overlay" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        "></div>

         <!-- Popup em si -->
        <div id="alert" style="
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            background-color: white;
            color: blue;
            border: 2px solid blue;
            border-radius: 40px;
            min-width: 300px;
            height: 270px;
            padding: 10px 100px 20px 100px;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 9999;
        ">
            <h2>' . htmlspecialchars($msg) . '</h2>
            ' . $linkHtml . '
        </div>
    ';
}
?>