<?php

/**
 * *************************************************************************
 * *                            Capture                                   **
 * *************************************************************************
 * @package     repository_capture                                        **
 * @subpackage  capture                                                   **
 * @name        Capture                                                   **
 * @copyright   oohoo.biz                                                 **
 * @link        http://oohoo.biz                                          **
 * @author      Nicolas Bretin                                            **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************ */

$string['btn_audio'] = 'Enregistrer un fichier audio';
$string['btn_help'] = 'Aide enregistrement';
$string['btn_help_help'] = 'Instructions d\'enregistrement :<br>
<ol>
<li>Utiliser le bouton d\'enregistrement pour enregistrer un son ou une vidéo</li>
<li>Le bouton "Lire" permet de réécouter l\'enregistrement</li>
<li>Utiliser les boutons triangulaires pour réduire l\'enregistrement à une seule partie de la vidéo</li>
<li>Une fois que l\'enregistrement est convenable, cliquer sur "Sauver" pour envoyer l\'enregistrement (Le traitement d\'envoi peut durer quelques secondes)</li>
</ol>
';
$string['btn_photo'] = 'Prendre une photo';
$string['btn_play'] = 'Lire/Pause';
$string['btn_record'] = 'Enregistrer';
$string['btn_record_photo'] = 'Photo';
$string['btn_reset'] = 'Réessayer';
$string['btn_save'] = 'Sauver';
$string['btn_settings'] = 'Paramètres d\'enregistrement';
$string['btn_stop'] = 'Stop';
$string['btn_video'] = 'Enregistrer une video';
$string['configplugin'] = 'Configuration pour le plugiciel Capture';
$string['default_filename'] = 'Enregistrement';
$string['download'] = 'Télécharger';
$string['err_no_hardware'] = 'La webcam ou le microphone est absent. Brancher votre materiel et rafraichisser la page (En fonction de votre navigateur, il sera sans doute nécessaire de le redemarrer.)';
$string['err_record_ffmpeg_exec'] = 'L\'execution de ffmpeg avec ce chemin a échoué. Vérifier le chemin et réessayer.';
$string['err_record_file_not_exists'] = 'L\'enregistrement n\'existe pas. Il est possible que le serveur ne soit pas bien configuré. Vérifier que le dossier temporaire est autorisé en écriture et que FFMPEG est bien installé.';
$string['err_record_fps_range'] = 'La valeur doit être comprise entre 10 et 60';
$string['err_record_quality_range'] = 'La valeur doit être comprise entre 20 et 100';
$string['head_index'] = 'Capture';
$string['pluginname'] = 'Capture';
$string['pluginname_help'] = 'Enregistrement un fichier audio ou vidéo directement dans Moodle et le téléverse dans le système de fichiers Moodle.';
$string['capture:audio'] = 'Utiliser le mode Audio de Capture dans le gestionnaire de fichiers';
$string['capture:photo'] = 'Utiliser le mode Photo de Capture dans le gestionnaire de fichiers';
$string['capture:video'] = 'Utiliser le mode Video de Capture audio dans le gestionnaire de fichiers';
$string['capture:view'] = 'Utiliser Capture audio dans le gestionnaire de fichiers';
$string['radio_no'] = 'Non';
$string['radio_yes'] = 'Oui';
$string['record_audio'] = 'Autoriser les enregistrements audio';
$string['record_audio_help'] = 'Sélectionner Oui pour autoriser les utilisateurs à enregistrer des fichiers audio avec leur microphone';
$string['record_ffmpeg'] = 'Chemin d\'execution de FFMPEG';
$string['record_ffmpeg_help'] = 'Le chemin d\'execution de ffmpeg(ou avconv en fonction de votre système). En géneral <b>ffmpeg</b> est suffisant mais sur certains serveur il sera necessaire d\inscrire le chemin complet comme <b>"C:\Program Files\ffmpeg\bin\ffmpeg.exe"</b> (les guillemets sont important s\'il y a des espaces dans le chemin)';
$string['record_fps'] = 'The nombre d\'images par seconde';
$string['record_fps_help'] = 'Regler le nombre d\'images par secondes. <b>Defaut = 15</b>.<br>
<b>Note</b> : Plus il y a d\'images par seconde, plus la taille de la vidéo sera grosse et le traitement de fin long. Max 60';
$string['record_photo'] = 'Autoriser la prise de photo avec la webcam';
$string['record_photo_help'] = 'Selectionner Oui pour autoriser les utilisateurs à prendre des photos avec leur webcam';
$string['record_quality'] = 'Qualité d\'enregistrement (in %)';
$string['record_quality_help'] = 'Defaut 70%. 100% étant la meilleur qualité.<br>
<b>Note</b> : Plus la qualité est élevée, plus il faudra du temps pour le traitement de fin';
$string['record_video'] = 'Autoriser l\'enregistrement vidéo';
$string['record_video_help'] = 'Sélectionner Oui pour autoriser les utilisateurs à enregistrer des vidéos avec leur webcam et microphone';
$string['saveas'] = 'Nom du fichier : ';
$string['setauthor'] = 'Auteur : ';
$string['setlicense'] = 'Licence : ';
$string['title_audio'] = 'Audio';
$string['title_audio_help'] = 'Cliquer sur Enregistrer un fichier audio pour enregistrer un nouveau fichier mp3 avec le microphone';
$string['title_info'] = 'Informations';
$string['title_info_help'] = 'Remplir ces informations avant de commencer un enregistrement';
$string['title_photo'] = 'Photo';
$string['title_photo_help'] = 'Choisir la taille de l\'image et cliquer sur Prendre une photo pour enregistrer une nouvelle image avec la webcam';
$string['title_video'] = 'Vidéo';
$string['title_video_help'] = 'Choisir la taille de la vidéo et cliquer sur Enregistrer une video pour enregistrer une nouvelle video avec la webcam et le microphone';
$string['video_conversion_processing'] = 'Traitement...';
$string['video_height'] = ' - Hauteur : ';
$string['video_width'] = 'Largeur : ';