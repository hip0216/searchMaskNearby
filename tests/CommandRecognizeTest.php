<?php
use PHPUnit\Framework\TestCase;

final class CommandRecognizeTest extends TestCase
{
    public function testMapToTableHeadersExpectMappingRightWhenInputArrayCountIsSingle(): void
    {
        $this->assertSame( ['成人口罩'], CommandRecognize::mapToTableHeaders(['a']) );
        $this->assertSame( ['成人口罩'], CommandRecognize::mapToTableHeaders(['adult']) );
        $this->assertSame( ['成人口罩'], CommandRecognize::mapToTableHeaders(['default']) );
        $this->assertSame( ['孩童口罩'], CommandRecognize::mapToTableHeaders(['c']) );
        $this->assertSame( ['孩童口罩'], CommandRecognize::mapToTableHeaders(['child']) );
        $this->assertSame( ['口罩總數'], CommandRecognize::mapToTableHeaders(['s']) );
        $this->assertSame( ['口罩總數'], CommandRecognize::mapToTableHeaders(['sum']) );
        $this->assertSame( ['機構名稱'], CommandRecognize::mapToTableHeaders(['i']) );
        $this->assertSame( ['機構名稱'], CommandRecognize::mapToTableHeaders(['institution']) );
        $this->assertSame( ['機構地址'], CommandRecognize::mapToTableHeaders(['d']) );
        $this->assertSame( ['機構地址'], CommandRecognize::mapToTableHeaders(['address']) );
    }
    
    public function testMapToTableHeadersExpectMappingRightWhenInputArrayCountIsMultiple(): void
    {
        $this->assertSame( ['成人口罩', '孩童口罩'], CommandRecognize::mapToTableHeaders(['a', 'c']) );
        $this->assertSame( ['孩童口罩', '成人口罩'], CommandRecognize::mapToTableHeaders(['c', 'a']) );
        $this->assertSame( ['機構地址', '成人口罩', '口罩總數', '機構名稱', '孩童口罩'], CommandRecognize::mapToTableHeaders(['d', 'a', 's', 'i', 'c']) );
    }

    public function testMapToTableHeadersExpectEmptyArrayWhenInputArrayIsEmpty(): void
    {
        $this->assertSame( [], CommandRecognize::mapToTableHeaders([]) );
    }

    // notActive
    public function mapToTableHeadersExpectFilteredArrayWhenInputArrayIsImpure(): void
    {
        $this->assertSame( ['機構名稱', '口罩總數', '成人口罩'], CommandRecognize::mapToTableHeaders(['dirty','institution', 'bad', 'sum', 'address']) );
        $this->assertSame( ['機構名稱', '口罩總數', '成人口罩'], CommandRecognize::mapToTableHeaders(['institution', 'hello', 'sum', 'address', 'hi']) );
        $this->assertSame( ['機構名稱', '口罩總數', '成人口罩'], CommandRecognize::mapToTableHeaders(['institution', 'sum', 'lol',  'address', 'sorry']) );
    }

    // Test case for sortRule
    private $table = [
        ['a' => 2, 'b' => 4, 'c' => 6],
        ['a' => 1, 'b' => 4, 'c' => 6],
        ['a' => 1, 'b' => 4, 'c' => 5],
        ['a' => 2, 'b' => 3, 'c' => 6],
        ['a' => 1, 'b' => 3, 'c' => 6],
        ['a' => 1, 'b' => 3, 'c' => 5],
        ['a' => 2, 'b' => 3, 'c' => 5],
        ['a' => 2, 'b' => 4, 'c' => 5],
    ];

    public function testSortRuleExpectMultiSortedIncreaseWhenSortInIncrease(): void
    {
        $table = $this->table;
        $cmdRcnz = new CommandRecognize();

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
        $table = $this->table;
        $cmdRcnz = new CommandRecognize();

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

    public function testNumericFilterExpectOnlyValueInRangeRemainWhenFilterWithSingleNumeric(): void
    {

        $expectA23 = [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
        ];
        $cmdRcnz = new CommandRecognize($this->table);
        $cmdRcnz->setFilterHeaders(['a']);
        $cmdRcnz->numericFilter(2, 3);
        $this->assertEquals($expectA23, $cmdRcnz->getTable());

        $expectB23 = [
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
        ];
        $cmdRcnz = new CommandRecognize($this->table);
        $cmdRcnz->setFilterHeaders(['b']);
        $cmdRcnz->numericFilter(2, 3);
        $this->assertEquals($expectB23, $cmdRcnz->getTable());

        $expectC47 =  [
            ['a' => 2, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 6],
            ['a' => 1, 'b' => 4, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 6],
            ['a' => 1, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 3, 'c' => 5],
            ['a' => 2, 'b' => 4, 'c' => 5],
        ];
        $cmdRcnz = new CommandRecognize($this->table);
        $cmdRcnz->setFilterHeaders(['c']);
        $cmdRcnz->numericFilter(4, 7);
        $this->assertEquals($expectC47, $cmdRcnz->getTable());
    }

    public function testNumericFilterExpectOnlyValueInRangeRemainWhenFilterWithMultiNumeric(): void
    {

        $expectA23 = [
            ['a' => 2, 'b' => 3, 'c' => 6],
            ['a' => 2, 'b' => 3, 'c' => 5],
        ];
        $cmdRcnz = new CommandRecognize($this->table);
        $cmdRcnz->setFilterHeaders(['a']);
        $cmdRcnz->numericFilter(2, 3);
        $cmdRcnz->setFilterHeaders(['b']);
        $cmdRcnz->numericFilter(2, 3);
        $this->assertEquals($expectA23, $cmdRcnz->getTable());
    }

    public function testNumericFilterExpectEmptyWhenNoResultWithMultiNumeric(): void
    {

        $expectA23 = [];
        $cmdRcnz = new CommandRecognize($this->table);
        $cmdRcnz->setFilterHeaders(['a']);
        $cmdRcnz->numericFilter(2, 3);
        $cmdRcnz->setFilterHeaders(['b']);
        $cmdRcnz->numericFilter(5, 6);
        $cmdRcnz->setFilterHeaders(['c']);
        $cmdRcnz->numericFilter(5, 6);
        $this->assertEquals($expectA23, $cmdRcnz->getTable());
    }

    // Test case for run (sort and filter)
    private $r0 = [
        ID => 0,
        INSTITUTION => '天堂健康服務中心',
        ADDRESS => '天堂路三段',
        PHONENUMBER => '(77)54875487',
        ADULT => 80,
        CHILD => 20,
        SUM => 100
    ];
    private $r1 = [
        ID => 1,
        INSTITUTION => '天堂衛生所',
        ADDRESS => '天地路七段',
        PHONENUMBER => '(77)27371666',
        ADULT => 0,
        CHILD => 50,
        SUM => 50
    ];
    private $r2 = [
        ID => 2,
        INSTITUTION => '地獄健康服務中心',
        ADDRESS => '地獄路三段',
        PHONENUMBER => '(66)66666666',
        ADULT => 666,
        CHILD => 666,
        SUM => 1332
    ];
    private $r3 = [
        ID => 3,
        INSTITUTION => '地獄衛生所',
        ADDRESS => '地獄路六段',
        PHONENUMBER => '(66)74539688',
        ADULT => 99,
        CHILD => 0,
        SUM => 99
    ];
    private $r4 = [
        ID => 4,
        INSTITUTION => '虛無殿堂',
        ADDRESS => '虛無之界',
        PHONENUMBER => '(00)00000000',
        ADULT => 99,
        CHILD => 20,
        SUM => 119
    ];

    public function testRunExpectSortWithSpecificHeaderNumericWhenValuesIsNumeric(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3]);
        $cmdRcnz->run(['sort' => ['a']]);
        $this->assertEquals([$r2, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['c']]);
        $this->assertEquals([$r2, $r1, $r0, $r3], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['s']]);
        $this->assertEquals([$r2, $r0, $r3, $r1], $cmdRcnz->getTable());

        $cmdRcnz->run(['sort' => ['adult']]);
        $this->assertEquals([$r2, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['child']]);
        $this->assertEquals([$r2, $r1, $r0, $r3], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['sum']]);
        $this->assertEquals([$r2, $r0, $r3, $r1], $cmdRcnz->getTable());
    }

    public function testRunExpectSortWithSpecificHeaderWhenValuesIsString(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3]);
        $cmdRcnz->run(['sort' => ['d']]);
        $this->assertEquals([$r0, $r1, $r3, $r2], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['i']]);
        $this->assertEquals([$r1, $r0, $r3, $r2], $cmdRcnz->getTable());

        $cmdRcnz->run(['sort' => ['address']]);
        $this->assertEquals([$r0, $r1, $r3, $r2], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['institution']]);
        $this->assertEquals([$r1, $r0, $r3, $r2], $cmdRcnz->getTable());
    }

    public function testRunExpectCanSortIncreaseAndDecreaseWhenSortWithSingleValue(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3]);
        $cmdRcnz->run(['sortDecrease' => ['a']]);
        $this->assertEquals([$r2, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(['sortIncrease' => ['a']]);
        $this->assertEquals([$r1, $r0, $r3, $r2], $cmdRcnz->getTable());
    }

    public function testRunExpectSortWithSpecificHeaderWhenSortWithMultiValue(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['sort' => ['a', 'c']]);
        $this->assertEquals([$r2, $r4, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(['sort' => ['c', 'a']]);
        $this->assertEquals([$r2, $r1, $r4, $r0, $r3], $cmdRcnz->getTable());
    }

    public function testRunExpectCanSortIncreaseAndDecreaseWhenSortWithMultiValue(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['sortDecrease' => ['a', 'c']]);
        $this->assertEquals([$r2, $r4, $r3, $r0, $r1], $cmdRcnz->getTable());
        $cmdRcnz->run(['sortDecrease' => ['c', 'a']]);
        $this->assertEquals([$r2, $r1, $r4, $r0, $r3], $cmdRcnz->getTable());
        $cmdRcnz->run(['sortIncrease' => ['a', 'c']]);
        $this->assertEquals([$r1, $r0, $r3, $r4, $r2], $cmdRcnz->getTable());
        $cmdRcnz->run(['sortIncrease' => ['c', 'a']]);
        $this->assertEquals([$r3, $r0, $r4, $r1, $r2], $cmdRcnz->getTable());
    }

    public function testRunExpectNumericFilterWorkWhenFilterNumeric(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['a' => [50, 100]]);
        $this->assertEquals([$r0, $r3, $r4], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['c' => [50, 100]]);
        $this->assertEquals([$r1], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['s' => [50, 100]]);
        $this->assertEquals([$r0, $r1, $r3], $cmdRcnz->getTable());

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['adult' => [50, 100]]);
        $this->assertEquals([$r0, $r3, $r4], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['child' => [50, 100]]);
        $this->assertEquals([$r1], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['sum' => [50, 100]]);
        $this->assertEquals([$r0, $r1, $r3], $cmdRcnz->getTable());
    }

    public function testRunExpectNumericFilterWorkWhenFilterNumericInputValusSingle(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['a' => [50]]);
        $this->assertEquals([$r0, $r2, $r3, $r4], $cmdRcnz->getTable());
    }

    public function testRunExpectAllPassArrayAndFilterExistWhenFilterStringNoKeyword(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['i' => []]);
        $this->assertEquals([$r0, $r1, $r2, $r3, $r4], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['institution' => []]);
        $this->assertEquals([$r0, $r1, $r2, $r3, $r4], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['d' => []]);
        $this->assertEquals([$r0, $r1, $r2, $r3, $r4], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['address' => []]);
        $this->assertEquals([$r0, $r1, $r2, $r3, $r4], $cmdRcnz->getTable());
    }

    public function testRunExpectEmptyArrayAndFilterExistWhenFilterStringNoKeyword(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['i' => ['早安']]);
        $this->assertEquals([], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['d' => ['天邊']]);
        $this->assertEquals([], $cmdRcnz->getTable());
    }

    public function testRunExpectStringFilterWorkWhenFilterStringSingle(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['i' => ['地獄']]);
        $this->assertEquals([$r2, $r3], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['d' => ['三段']]);
        $this->assertEquals([$r0, $r2], $cmdRcnz->getTable());
    }

    public function testRunExpectStringFilterWorkWhenFilterStringMulti(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['i' => ['地獄', '中心']]);
        $this->assertEquals([$r2], $cmdRcnz->getTable());
        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['d' => ['地', '段']]);
        $this->assertEquals([$r1, $r2, $r3], $cmdRcnz->getTable());
    }

    public function testRunExpectStringFilterLayeredWorkWhenFilterStringMulti(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['d' => ['地', '段']]);
        $cmdRcnz->run(['d' => ['獄']]);
        $this->assertEquals([$r2, $r3], $cmdRcnz->getTable());
    }

    public function testRunExpectPassWhenMultiCommandExist(): void 
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['a'=> [10], 'd' => ['地']]);
        $this->assertEquals([$r2, $r3], $cmdRcnz->getTable());
    }

    public function testRunExpectReturnSizeRestrictedWhenSetReturnLimit(): void
    {
        $r0 = $this->r0;
        $r1 = $this->r1;
        $r2 = $this->r2;
        $r3 = $this->r3;
        $r4 = $this->r4;

        $cmdRcnz = new CommandRecognize([$r0, $r1, $r2, $r3, $r4]);
        $cmdRcnz->run(['returnLimit' => [2]]);
        $this->assertEquals([$r0, $r1], $cmdRcnz->getTable());
    }
}
