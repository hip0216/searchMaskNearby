<?php
use PHPUnit\Framework\TestCase;

final class MapKeyTest extends TestCase
{
    public function testmapToTableHeadersExpectMappingRightWhenInputArrayCountIsSingle(): void
    {
        $this->assertEquals( ['成人口罩'], mapToTableHeaders(['a']) );
        $this->assertEquals( ['成人口罩'], mapToTableHeaders(['adult']) );
        $this->assertEquals( ['成人口罩'], mapToTableHeaders(['default']) );
        $this->assertEquals( ['孩童口罩'], mapToTableHeaders(['c']) );
        $this->assertEquals( ['孩童口罩'], mapToTableHeaders(['child']) );
        $this->assertEquals( ['口罩總數'], mapToTableHeaders(['s']) );
        $this->assertEquals( ['口罩總數'], mapToTableHeaders(['sum']) );
        $this->assertEquals( ['機構名稱'], mapToTableHeaders(['i']) );
        $this->assertEquals( ['機構名稱'], mapToTableHeaders(['institution']) );
        $this->assertEquals( ['機構地址'], mapToTableHeaders(['d']) );
        $this->assertEquals( ['機構地址'], mapToTableHeaders(['address']) );
    }
    
    public function testmapToTableHeadersExpectMappingRightWhenInputArrayCountIsMultiple(): void
    {
        $this->assertEquals( ['成人口罩', '孩童口罩'], mapToTableHeaders(['a', 'c']) );
        $this->assertEquals( ['孩童口罩', '成人口罩'], mapToTableHeaders(['c', 'a']) );
        $this->assertEquals( ['機構地址', '成人口罩', '口罩總數', '機構名稱', '孩童口罩'], mapToTableHeaders(['d', 'a', 's', 'i', 'c']) );
    }

    public function testmapToTableHeadersExpectEmptyArrayWhenInputArrayIsEmpty(): void
    {
        $this->assertEquals( [], mapToTableHeaders([]) );
    }

    public function mapToTableHeadersExpectFilteredArrayWhenInputArrayIsImpure(): void
    {
        $this->assertEquals( ['機構名稱', '口罩總數', '成人口罩'], mapToTableHeaders(['dirty','institution', 'bad', 'sum', 'address']) );
        $this->assertEquals( ['機構名稱', '口罩總數', '成人口罩'], mapToTableHeaders(['institution', 'hello', 'sum', 'address', 'hi']) );
        $this->assertEquals( ['機構名稱', '口罩總數', '成人口罩'], mapToTableHeaders(['institution', 'sum', 'lol',  'address', 'sorry']) );
    }
}
