<?php

define("APP_NAME", "ClinicDesk");

define("BASE_URL", "http://localhost/clinicdesk/");

define("ITEMS_PER_PAGE", 10);

define("MAX_IMAGE_SIZE", 1 * 1024 * 1024);
define("MAX_PDF_SIZE",   3 * 1024 * 1024);

define("UPLOAD_AVATAR_PATH", __DIR__ . "/../public/uploads/avatars/");
define("UPLOAD_DOCTOR_PHOTO_PATH", __DIR__ . "/../public/uploads/doctor_photos/");
define("UPLOAD_PRESCRIPTION_PATH", __DIR__ . "/../public/uploads/prescriptions/");

$TIME_SLOTS = [
    "09:00","09:30","10:00","10:30","11:00","11:30",
    "12:00","12:30","13:00","13:30","14:00","14:30",
    "15:00","15:30","16:00"
];

ini_set("display_errors", "1");
error_reporting(E_ALL);

date_default_timezone_set("Asia/Gaza");
