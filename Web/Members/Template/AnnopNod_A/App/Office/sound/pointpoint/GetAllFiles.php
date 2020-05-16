<?php
  $files = glob("*.{mp3,MP3,wav,WAV,ogg,OGG}", GLOB_BRACE);
  echo json_encode($files);