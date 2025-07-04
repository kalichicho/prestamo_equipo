<?php
date_default_timezone_set('Europe/Madrid');
// 1) Fuentes RSS gratuitas
$rssFeeds = [
  'NVD'              => 'https://nvd.nist.gov/feeds/xml/cve/misc/nvd-rss.xml',
  'CERT-ES'          => 'https://www.incibe.es/rss/noticias.xml',
  'BleepingComputer' => 'https://www.bleepingcomputer.com/feed/',
];
// 2) Software a vigilar
$softwares = [
  '7zip','chrome','firefox','edge',
  'forticlient','aruba','crearpass',
  'office 2016','office 2010','office 365',
  'windows 10','windows 11','q-gis'
];
// 3) Conexión PDO (PHP 8+)
$pdo = new PDO(
  'mysql:host=127.0.0.1;dbname=tu_bd;charset=utf8',
  'usuario','pass',
  [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
$insert = $pdo->prepare("
  INSERT IGNORE INTO notificaciones
    (software, fuente, titulo, enlace, descripcion, fecha)
  VALUES
    (:software, :fuente, :titulo, :enlace, :descripcion, :fecha)
");
// 4) Procesar cada RSS
foreach ($rssFeeds as $fuente => $url) {
  libxml_use_internal_errors(true);
  $xml = @simplexml_load_file($url);
  if (!$xml) continue;
  $items = $xml->channel->item ?? $xml->entry ?? [];
  foreach ($items as $item) {
    $title = (string)($item->title ?? $item->summary);
    $link  = (string)($item->link['href'] ?? $item->link);
    $desc  = strip_tags((string)($item->description ?? $item->summary));
    $date  = date('Y-m-d H:i:s', strtotime((string)($item->pubDate ?? $item->updated)));
    foreach ($softwares as $sw) {
      if (stripos($title, $sw)!==false || stripos($desc, $sw)!==false) {
        // Sólo última 48 h
        if (time() - strtotime($date) <= 48*3600) {
          $insert->execute([
            ':software'    => ucfirst($sw),
            ':fuente'      => $fuente,
            ':titulo'      => $title,
            ':enlace'      => $link,
            ':descripcion' => mb_strimwidth($desc, 0, 200, '…'),
            ':fecha'       => $date
          ]);
        }
        break;
      }
    }
  }
}
