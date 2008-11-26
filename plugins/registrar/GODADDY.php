<?php

/*

    This plugin should send the specified e-mail a notice alerting them that
    the domain needs to be registered,renewed, or transfered manually.

    ============================================================================
    Email templates:
    ============================================================================

    Register ...................................................................
                registrar_{THIS}_register_user
                registrar_{THIS}_register_admin (or false)

    Renew ......................................................................
                registrar_{THIS}_renew_user
                registrar_{THIS}_renew_admin    (or false)

    Transfer....................................................................
                registrar_{THIS}_transfer_user
                registrar_{THIS}_transfer_admin (or false)


    ============================================================================
    Available Config Variables:
    ============================================================================
    debug (bool)
    ns1 (primary nameserver)
    ns2 (secondary nameserver)
    ns1ip (primary nameserver ip)
    ns2ip (secondary nameserver ip)

    gd_user
    gd_pass
    gd_mode (0/1 test/live)

    ============================================================================
*/

?>
