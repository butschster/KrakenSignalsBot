<?php

namespace Tests\Unit\Services\Imap;

use App\Contracts\Services\Kraken\Order;
use App\Services\Imap\MessageContentParser;
use Tests\TestCase;

class MessageContentParserTest extends TestCase
{
    function test_parse_text_string()
    {
        $parser = new MessageContentParser();

        $information = $parser->parse('26.02.2018 XBTCZEUR BUY 7');

        $this->assertEquals('2018-02-26', $information->getDate()->toDateString());
        $this->assertEquals('XBTCZEUR', $information->getPair());
        $this->assertEquals(Order::TYPE_BUY, $information->getType());
        $this->assertEquals(7, $information->getVolume());
    }

    function test_parse_html_string()
    {
        $parser = new MessageContentParser();

        $html = <<<EOL
<html>
<head>

<style>

.form-title    {
    background-color: #ffffff;
    color: #141414;
    font-weight: bold;
}

.form-field-caption {
    font-style:italic;
}

.table-row {
    background-color: #f1f3f7;
}

.table-head {
    background-color: #bbbbbb;
}

</style>
</head>
<body>26.02.2018 XBTCZEUR SELL 7</body>
</html>
EOL;

        $information = $parser->parse($html);

        $this->assertEquals('2018-02-26', $information->getDate()->toDateString());
        $this->assertEquals('XBTCZEUR', $information->getPair());
        $this->assertEquals(Order::TYPE_SELL, $information->getType());
        $this->assertEquals(7, $information->getVolume());
    }
}
