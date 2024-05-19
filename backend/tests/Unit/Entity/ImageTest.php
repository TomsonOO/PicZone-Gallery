<?php
use App\Entity\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase {
    public function testSetDescription() {
        $image = new Image();
        $description = "A detailed description";
        $image->setDescription($description);
        $this->assertEquals($description, $image->getDescription());
    }

    public function testIsActiveDefault() {
        $image = new Image();
        $this->assertFalse($image->isActive());
    }
}
