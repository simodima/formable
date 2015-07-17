<?php
namespace Formable\Tests\Integration;

use Formable\Bundle\Cache\FullScanNamespaceAnnotationWarmer;

class FullScanNamespaceAnnotationWarmerTest extends BaseIntegration
{
    /**
     * @test
     */
    public function it_should_scan()
    {
        $reader = $this->getMock('\Doctrine\Common\Annotations\Reader');
        $reader->expects($this->exactly(7))
            ->method('getPropertyAnnotation')
            ;

        $warmer = new FullScanNamespaceAnnotationWarmer($reader, [__DIR__.DIRECTORY_SEPARATOR.'DTOs']);
        $warmer->warmUp(null);
    }
}