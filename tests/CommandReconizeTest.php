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
        $table = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
        ];
        $cmdRcnz = new CommandReconize();

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
        $cmdRcnz->setSortInIncrease(true);
        $cmdRcnz->setSortHeaders(['a', 'b', 'c']);
        usort($table, [$cmdRcnz, 'sortRule']);
        $this->assertEquals($expectIncAbc, $table);

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
        $cmdRcnz->setSortInIncrease(true);
        $cmdRcnz->setSortHeaders(['c', 'a', 'b']);
        usort($table, [$cmdRcnz, 'sortRule']);
        $this->assertEquals($expectIncCab, $table);
    }

    public function testSortRuleExpectMultiSortedDecreaseWhenSortInDecrease(): void
    {
        $table = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
        ];
        $cmdRcnz = new CommandReconize();

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
        $cmdRcnz->setSortInIncrease(false);
        $cmdRcnz->setSortHeaders(['a', 'b', 'c']);
        usort($table, [$cmdRcnz, 'sortRule']);
        $this->assertEquals($expectDecAbc, $table);
        
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
        $cmdRcnz->setSortInIncrease(false);
        $cmdRcnz->setSortHeaders(['b', 'a', 'c']);
        usort($table, [$cmdRcnz, 'sortRule']);
        $this->assertEquals($expectDecBac, $table);
    }

    public function testRunExpectNoWhenNo(): void
    {
        $r0 = [
            ID => 0,
            INSTITUTION => '天堂健康服務中心',
            ADDRESS => '天堂路三段',
            PHONENUMBER => '(77)54875487',
            ADULT => 80,
            CHILD => 20,
            SUM => 100
        ];
        $r1 = [
            ID => 1,
            INSTITUTION => '天堂衛生所',
            ADDRESS => '天地路七段',
            PHONENUMBER => '(77)27371666',
            ADULT => 0,
            CHILD => 50,
            SUM => 50
        ];
        $r2 = [
            ID => 2,
            INSTITUTION => '地獄健康服務中心',
            ADDRESS => '地獄路三段',
            PHONENUMBER => '(66)66666666',
            ADULT => 666,
            CHILD => 666,
            SUM => 1332
        ];
        $r3 = [
            ID => 3,
            INSTITUTION => '地獄衛生所',
            ADDRESS => '地獄路六段',
            PHONENUMBER => '(66)74539688',
            ADULT => 99,
            CHILD => 0,
            SUM => 99
        ];

        $cmdRcnz = new CommandReconize([$r0, $r1, $r2, $r3]);
        $cmdRcnz->run(["sort" => ['a']]);
        $this->assertEquals([$r2, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(["sort" => ['c']]);
        $this->assertEquals([$r2, $r1, $r0, $r3], $cmdRcnz->getTable());
        $cmdRcnz->run(["sort" => ['s']]);
        $this->assertEquals([$r2, $r0, $r3, $r1], $cmdRcnz->getTable());

        // $cmdRcnz->run(["sort" => ['d']]);
        // $this->assertEquals([$r2, $r0, $r3, $r1], $cmdRcnz->getTable());
        // $cmdRcnz->run(["sort" => ['i']]);

        $cmdRcnz->run(["sort" => ['adult']]);
        $this->assertEquals([$r2, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(["sort" => ['child']]);
        $this->assertEquals([$r2, $r1, $r0, $r3], $cmdRcnz->getTable());
        $cmdRcnz->run(["sort" => ['sum']]);
        $this->assertEquals([$r2, $r0, $r3, $r1], $cmdRcnz->getTable());

        // $cmdRcnz->run(["sort" => ['address']]);
        // $cmdRcnz->run(["sort" => ['institution']]);
        //$this->assertEquals(, );
    }
}
