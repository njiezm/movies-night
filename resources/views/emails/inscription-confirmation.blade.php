<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
 <head> 
  <meta charset="UTF-8"> 
  <meta content="width=device-width, initial-scale=1" name="viewport"> 
  <meta name="x-apple-disable-message-reformatting"> 
  <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
  <meta content="telephone=no" name="format-detection"> 
  <title>DauphinTélécom</title> 
 </head>
 <style type="text/css">
    body {
        font-family: Arial, sans-serif;
        color: #000;
        background-color: #fff;
        margin: 0;
        padding: 0;
    }
    h3 {
        color: #321834; /* rose vif */
        text-align: center;
        margin-bottom: 15px;
    }
    h4 {
        color: #321834;
        margin-top: 20px;
    }
    p {
        line-height: 1.5;
        font-size: 14px;
        margin: 10px 0;
    }
    ul {
        padding-left: 20px;
    }
    li {
        margin: 5px 0;
    }
    .btn {
        display: inline-block;
        background-color: #321834;
        color: #fff !important;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 4px;
        margin-top: 15px;
    }
    img {
        display: block;
        margin: 0 auto 20px auto;
        max-width: 100%;
        height: auto;
    }
  
</style>

 <body>
    <center>
        <div style="color:#000 !important;">
            
            <div style="margin-top: 20px">
                  <img src="{{ asset('images/madiana/headermail.png') }}" style="max-width: 400px;" alt="Header Image">
               <h3>Bonjour {{ $firstname }},</h3>
        
        <p>Merci pour votre inscription au <strong>Marathon de l’Horreur</strong> de Madiana !</p>
        
        <p>Votre participation est bien enregistrée. Il ne vous reste plus qu’à vivre l’expérience en salle… si vous l’osez 😱</p>
        
        <h4>Le principe est simple :</h4>
        <ul>
            <li>Découvrez la <strong>sélection des 6 films d’horreur</strong></li>
            <li>Rendez-vous au Madiana pour les visionner</li>
            <li><strong>Scannez le QR code</strong> affiché avant chaque séance pour valider votre participation</li>
            <li>Tentez de gagner <strong>des places tous les mois</strong>, et peut-être <strong>1 an de cinéma</strong> si vous relevez le défi jusqu’au bout</li>
        </ul>

        <p>
          👉 <a href="#" class="btn">Voir la sélection des 6 films</a>
        </p>

        <p>Installez-vous confortablement, gardez l’œil ouvert… et que le frisson commence 🍿🩸</p>
        <p>Bonne chance et à très bientôt à Madiana !</p>

            </div>
            <div style="margin-top: 40px;">
                L'équipe de Madiana
            </div>
        </div>
    </center>
 </body>
</html>