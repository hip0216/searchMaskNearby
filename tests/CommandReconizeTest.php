<?php
use PHPUnit\Framework\TestCase;

final class CommandReconizeTest extends TestCase
{
    public function testMapToTableHeadersExpectMappingRightWhenInputArrayCountIsSingle(): void
    {
        $this->assertSame( ['成人口罩'], CommandReconize::mapToTableHeaders(['a']) );
        $this->assertSame( ['成人口罩'], CommandReconize::mapToTableHeaders(['adult']) );
        $this->assertSame( ['成人口罩'], CommandReconize::mapToTableHeaders(['default']) );
        $this->assertSame( ['孩童口罩'], CommandReconize::mapToTableHeaders(['c']) );
        $this->assertSame( ['孩童口罩'], CommandReconize::mapToTableHeaders(['child']) );
        $this->assertSame( ['口罩總數'], CommandReconize::mapToTableHeaders(['s']) );
        $this->assertSame( ['口罩總數'], CommandReconize::mapToTableHeaders(['sum']) );
        $this->assertSame( ['機構名稱'], CommandReconize::mapToTableHeaders(['i']) );
        $this->assertSame( ['機構名稱'], CommandReconize::mapToTableHeaders(['institution']) );
        $this->assertSame( ['機構地址'], CommandReconize::mapToTableHeaders(['d']) );
        $this->assertSame( ['機構地址'], CommandReconize::mapToTableHeaders(['address']) );
    }
    
    public function testMapToTableHeadersExpectMappingRightWhenInputArrayCountIsMultiple(): void
    {
        $this->assertSame( ['成人口罩', '孩童口罩'], CommandReconize::mapToTableHeaders(['a', 'c']) );
        $this->assertSame( ['孩童口罩', '成人口罩'], CommandReconize::mapToTableHeaders(['c', 'a']) );
        $this->assertSame( ['機構地址', '成人口罩', '口罩總數', '機構名稱', '孩童口罩'], CommandReconize::mapToTableHeaders(['d', 'a', 's', 'i', 'c']) );
    }

    public function testMapToTableHeadersExpectEmptyArrayWhenInputArrayIsEmpty(): void
    {
        $this->assertSame( [], CommandReconize::mapToTableHeaders([]) );
    }

    // notActive
    public function mapToTableHeadersExpectFilteredArrayWhenInputArrayIsImpure(): void
    {
        $this->assertSame( ['機構名稱', '口罩總數', '成人口罩'], CommandReconize::mapToTableHeaders(['dirty','institution', 'bad', 'sum', 'address']) );
        $this->assertSame( ['機構名稱', '口罩總數', '成人口罩'], CommandReconize::mapToTableHeaders(['institution', 'hello', 'sum', 'address', 'hi']) );
        $this->assertSame( ['機構名稱', '口罩總數', '成人口罩'], CommandReconize::mapToTableHeaders(['institution', 'sum', 'lol',  'address', 'sorry']) );
    }

    public function testSortRuleExpectMultiSortedIncreaseWhenSortInIncrease(): void
    {
        $input = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
        ];
        global $sortInIncrease;  // bool
        global $sortHeaders;  // array of Headers

        $expectIncAbc = [
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 2, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 6],
        ];
        $sortInIncrease = true;
        $sortHeaders = ['a', 'b', 'c'];
        usort($input, 'CommandReconize::sortRule');
        $this->assertEquals($expectIncAbc, $input);

        $expectIncCab = [
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 2, 'b' => 4, 'c' => 6],
        ];
        $sortInIncrease = true;
        $sortHeaders = ['c', 'a', 'b'];
        usort($input, 'CommandReconize::sortRule');
        $this->assertEquals($expectIncCab, $input);
    }

    public function testSortRuleExpectMultiSortedDecreaseWhenSortInDecrease(): void
    {
        $input = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
        ];
        global $sortInIncrease;  // bool
        global $sortHeaders;  // array of Headers

        $expectDecAbc = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 2, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
        ];
        $sortInIncrease = false;
        $sortHeaders = ['a', 'b', 'c'];
        usort($input, 'CommandReconize::sortRule');
        $this->assertEquals($expectDecAbc, $input);
        
        $expectDecBac = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 2, 'b' => 4, 'c' => 5],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
        ];
        $sortInIncrease = false;
        $sortHeaders = ['b', 'a', 'c'];
        usort($input, 'CommandReconize::sortRule');
        $this->assertEquals($expectDecBac, $input);
    }
}
