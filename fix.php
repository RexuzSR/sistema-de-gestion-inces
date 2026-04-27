<?php
$file = 'assets/js/Reporte/RE-Curso-Trayectoria.js';
$content = file_get_contents($file);

$search = "            }
        }

function curTrayRenderDetalle";

$replace = "            }
        }
    });
}

function curTrayRenderDetalle";

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Fixed JS syntax error again.";
