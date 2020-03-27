<?php
use PHPUnit\Framework\TestCase;

final class MapKeyTest extends TestCase
{
    public function testMapKeyExpectMappingRightWhenInputArrayCountIsSingle(): void
    {
        $this->assertEquals( ['成人口罩'], mapKey(['a']) );
        $this->assertEquals( ['成人口罩'], mapKey(['adult']) );
        $this->assertEquals( ['成人口罩'], mapKey(['default']) );
        $this->assertEquals( ['孩童口罩'], mapKey(['c']) );
        $this->assertEquals( ['孩童口罩'], mapKey(['child']) );
        $this->assertEquals( ['口罩總數'], mapKey(['s']) );
        $this->assertEquals( ['口罩總數'], mapKey(['sum']) );
        $this->assertEquals( ['機構名稱'], mapKey(['i']) );
        $this->assertEquals( ['機構名稱'], mapKey(['institution']) );
        $this->assertEquals( ['機構地址'], mapKey(['d']) );
        $this->assertEquals( ['機構地址'], mapKey(['address']) );
    }
    
    public function testMapKeyExpectMappingRightWhenInputArrayCountIsMultiple(): void
    {
        $this->assertEquals( ['成人口罩', '孩童口罩'], mapKey(['a', 'c']) );
        $this->assertEquals( ['孩童口罩', '成人口罩'], mapKey(['c', 'a']) );
        $this->assertEquals( ['機構地址', '成人口罩', '口罩總數', '機構名稱', '孩童口罩'], mapKey(['d', 'a', 's', 'i', 'c']) );
    }

    public function testMapKeyExpectEmptyArrayWhenInputArrayIsEmpty(): void
    {
        $this->assertEquals( [], mapKey([]) );
    }

    public function MapKeyExpectFilteredArrayWhenInputArrayIsImpure(): void
    {
        $this->assertEquals( ['機構名稱', '口罩總數', '成人口罩'], mapKey(['dirty','institution', 'bad', 'sum', 'address']) );
        $this->assertEquals( ['機構名稱', '口罩總數', '成人口罩'], mapKey(['institution', 'hello', 'sum', 'address', 'hi']) );
        $this->assertEquals( ['機構名稱', '口罩總數', '成人口罩'], mapKey(['institution', 'sum', 'lol',  'address', 'sorry']) );
    }
}
