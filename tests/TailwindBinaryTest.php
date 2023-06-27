<?php

/*
 * This file is part of the SymfonyCasts TailwindBundle package.
 * Copyright (c) SymfonyCasts <https://symfonycasts.com/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfonycasts\TailwindBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfonycasts\TailwindBundle\TailwindBinary;

class TailwindBinaryTest extends TestCase
{
    public function testBinaryIsDownloadedAndProcessCreated()
    {
        $binaryDownloadDir = __DIR__.'/fixtures/download';
        $fs = new Filesystem();
        if (file_exists($binaryDownloadDir)) {
            $fs->remove($binaryDownloadDir);
        }
        $fs->mkdir($binaryDownloadDir);

        $client = new MockHttpClient([
            new MockResponse('fake binary contents'),
        ]);

        $binary = new TailwindBinary($binaryDownloadDir, __DIR__, null, null, $client);
        $process = $binary->createProcess(['-i', 'fake.css']);
        $this->assertFileExists($binaryDownloadDir.'/'.TailwindBinary::getBinaryName());
        $this->assertSame(
            sprintf("'%s' '-i' 'fake.css'", $binaryDownloadDir.'/'.TailwindBinary::getBinaryName()),
            $process->getCommandLine()
        );
    }

    public function testCustomBinaryUsed()
    {
        $client = new MockHttpClient();

        $binary = new TailwindBinary('', __DIR__, 'custom-binary', null, $client);
        $process = $binary->createProcess(['-i', 'fake.css']);
        $this->assertSame(
            "'custom-binary' '-i' 'fake.css'",
            $process->getCommandLine()
        );
    }
}
