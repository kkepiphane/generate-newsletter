<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

//connexion à la de données
require_once("class/Bdd.php");
$bd = new Bdd();
$bdd = $bd->connect();

// Répertoire d'origine des fichiers traités
$sourceDirectory = '/home/data_news_input/';

// Répertoire de destination pour les fichiers traités
$destinationDirectory = 'data_news/';

$csvFiles = glob($sourceDirectory . '*.csv');


foreach ($csvFiles as $csvFile) {
    $dataGroups = [];
    $line_count = 0;
    $datacontent = '';

    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        if (($header = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $titleIndex = array_search('Title', $header);
            $descriptionIndex = array_search('Content', $header);
            $priceIndex = array_search('listivo_7874_listivo_13', $header);
            $imagesIndex = array_search('Image URL', $header);
            $addressIndex = array_search('listivo_153_address', $header);
            $langIndex = array_search('lang', $header);
            $dateIndex = array_search('date_pub', $header);

            while (($datas = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $address = $datas[$addressIndex];
                $datesend = $datas[$dateIndex];
                $dataGroups[$address][$datesend][] = $datas;
            }
        }
        fclose($handle);

        foreach ($dataGroups as $address => $dates) {
            foreach ($dates as $datesend => $rows) {
                foreach ($rows as $data) {
                    if ($line_count == 0) {
                        if ($data[$langIndex] == 'fr') {
                            $pricetitle = "à partir de ";
                            $templates = file_get_contents('template_fr.html');
                            $pricesymbole = "€";
                        } elseif ($data[$langIndex] == 'en') {
                            $pricetitle = "from ";
                            $pricesymbole = "$ ";
                            $templates = file_get_contents('template_en.html');
                        } elseif ($data[$langIndex] == 'de') {
                            $pricetitle = "ab ";
                            $pricesymbole = "€";
                            $templates = file_get_contents('template_de.html');
                        } else {
                            $templates = file_get_contents('template_en.html');
                            $pricetitle = "from ";
                            $pricesymbole = "€";
                        }
                    }

                    $datacontent .= '<tr>
                        <td align="left" bgcolor="#f5f5f5" style="Margin:0;padding-bottom:10px;padding-top:20px;padding-left:20px;padding-right:20px;background-color:#f5f5f5">
                            <table cellpadding="0" cellspacing="0" class="es-left" align="left" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                                <tr>
                                    <td align="left" style="padding:0;Margin:0;width:270px">
                                        <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;background-color:#ffffff;border-radius:10px 10px 0px 0px" role="presentation">
                                            <tr>
                                                <td align="center" style="padding:0;Margin:0;font-size:0px">
                                                    <img class="adapt-img" src="'. htmlspecialchars($data[$imagesIndex]) .'" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;border-radius:10px 10px 0px 0px" width="270" height="180">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <table cellpadding="0" cellspacing="0" class="es-right" align="right" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                                <tr>
                                    <td align="left" style="padding:0;Margin:0;width:270px">
                                        <table cellpadding="0" cellspacing="0" width="100%" bgcolor="#ffffff" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:separate;border-spacing:0px;background-color:#ffffff;border-radius:0px 0px 10px 10px" role="presentation">
                                            <tr>
                                                <td align="left" style="Margin:0;padding-bottom:5px;padding-top:15px;padding-left:15px;padding-right:15px">
                                                    <h3 style="Margin:0;line-height:22px;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-size:18px;font-style:normal;font-weight:bold;color:#005ABF">'.htmlspecialchars($data[$titleIndex]) .' <span style="color:#333333">Discovery</span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0;Margin:0">
                                                    <table cellpadding="0" cellspacing="0" width="100%" class="es-menu" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                                                        <tr class="links-images-left">
                                                            <td align="left" valign="top" width="100%" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:15px;padding-right:15px;border:0" id="esd-menu-id-1">
                                                                <p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;line-height:21px;color:#333333;font-size:12px;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic;padding-right:5px;vertical-align:middle">'. htmlspecialchars($data[$descriptionIndex]) .'</p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="left" style="Margin:0;padding-top:5px;padding-bottom:5px;padding-left:15px;padding-right:15px">
                                                    <h3 style="Margin:0;line-height:22px;mso-line-height-rule:exactly;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-size:18px;font-style:normal;font-weight:bold;color:#005ABF">'. htmlspecialchars($pricetitle) .'<span style="color:#A9A9A9">'. htmlspecialchars($pricesymbole) .''. htmlspecialchars($data[$priceIndex]) .'</span></h3>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="padding:0;Margin:0;font-size:0px">
                                                    <img class="adapt-img" src="https://fpsji.stripocdn.email/content/guids/CABINET_5f0ff7ddd10f00ba70ae819f2e5b0be2a8e833d5bc121a018227b19d23133762/images/5423423.png" alt style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" width="270" height="26">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="Margin:0;padding-top:5px;padding-bottom:15px;padding-left:15px;padding-right:15px">
                                                    <span class="msohide es-button-border" style="border-style:solid;border-color:#2CB543;background:#FBCB2A;border-width:0px;display:block;border-radius:5px;width:auto;mso-hide:all">
                                                        <a href="" class="es-button msohide" target="_blank" style="mso-style-priority:100 !important;text-decoration:none;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;color:#090909;font-size:14px;padding:13px 30px 13px 30px;display:block;background:#FBCB2A;border-radius:5px;font-family:arial, \'helvetica neue\', helvetica, sans-serif;font-weight:bold;font-style:normal;line-height:17px;width:auto;text-align:center;mso-padding-alt:0;mso-border-alt:10px solid #FBCB2A;mso-hide:all;padding-left:5px;padding-right:5px">Voir plus</a>
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>';

                    $line_count++;

                    if ($line_count == 10) {
                        $template_news = str_replace('<div id="pub"></div>', $datacontent, $templates);

                        $insertStmt = $bdd->prepare('INSERT INTO news_template (address, template, date_send, date_input) VALUES (?, ?, ?, NOW())');
                        $insertStmt->execute([$address, $template_news, $datesend]);

                        $datacontent = '';
                        $line_count = 0;
                    }
                }

                if ($line_count > 0) {
                    $template_news = str_replace('<div id="pub"></div>', $datacontent, $templates);

                    $insertStmt = $bdd->prepare('INSERT INTO news_template (address, template, date_send, date_input) VALUES (?, ?, ?, NOW())');
                    $insertStmt->execute([$address, $template_news, $datesend]);

                    $datacontent = '';
                    $line_count = 0;
                }
            }
        }

        $destinationFile = $destinationDirectory . basename($csvFile);
        if (rename($csvFile, $destinationFile)) {
            echo "Le fichier $csvFile a été déplacé vers $destinationFile\n";
        } else {
            echo "Erreur lors du déplacement du fichier $csvFile\n";
        }
    }
}
?>
