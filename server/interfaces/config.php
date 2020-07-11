<?php

namespace server\interfaces;

interface config{
    /* tells application the root path to operate on */
    const path = ".";
    const host = "database_host_name_here";
    const database = "database_name_here";
    const user = "user_database";
    const password = "password_database";
    const captcha = "enable";
    const secret = "secret_here";
    const domain = "domain_to_verify_captcha";
}

?>