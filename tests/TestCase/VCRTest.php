<?php

use PHPUnit\Framework\TestCase;
use VCR\VCR;

class VCRTest extends TestCase
{
    const URL = 'https://www.google.com';

    /**
     * @test
     */
    public function curlRequest() : void
    {
        VCR::turnOn();

        $cassetteName = 'curl.yaml';
        VCR::insertCassette($cassetteName);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, static::URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $expected = curl_exec($ch);
        curl_close($ch);

        VCR::eject();
        VCR::turnOff();

        $this->assertSameBody($expected, $cassetteName);
    }

    /**
     * @test
     */
    public function fileGetContentsRequest() : void
    {
        VCR::turnOn();

        $cassetteName = 'file_get_contents.yaml';
        VCR::insertCassette($cassetteName);

        $expected = file_get_contents(static::URL);

        VCR::eject();
        VCR::turnOff();

        $this->assertSameBody($expected, $cassetteName);
    }

    /**
     * @param string $expected
     * @param string $cassetteName
     */
    protected function assertSameBody(string $expected, string $cassetteName) : void
    {
        $yamlObjs = new \VCR\Storage\Yaml(dirname(__DIR__) . '/fixtures', $cassetteName);
        $actual = [];
        foreach ($yamlObjs as $obj) {
            $actual[] = $obj;
        }

        $body = $actual[0]['response']['body'];
        $this->assertSame($expected, $body);
    }
}
