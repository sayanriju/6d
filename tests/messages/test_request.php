<?php

//error_log("Test request was received" . PHP_EOL . http_build_query($_REQUEST));
echo http_build_query($_REQUEST);
