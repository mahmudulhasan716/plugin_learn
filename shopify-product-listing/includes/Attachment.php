<?php

namespace WeLabs\ShopifyProductListing;

use Exception;
use WC_Product_Importer;

class Attachment extends WC_Product_Importer {
    public function import() {
        throw new Exception( 'It does not support import. This is used as a helper' );
    }
}
