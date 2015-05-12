<?php

namespace Fieg\Domain\Component;

class DependencySolverTest extends \PHPUnit_Framework_TestCase
{
    public function testSolve()
    {
        $component1 = $this->createComponentMock();
        $component2 = $this->createComponentMock([$component1]);

        $components[] = $component2;
        $components[] = $component1;

        $solver = new DependencySolver();

        $result = $solver->solve($components);

        $this->assertEquals([$component1, $component2], $result);
    }

    /**
     * @expectedException \LogicException
     */
    public function testCircularRequirement()
    {
        $component1 = $this->createComponentMock(false);
        $component3 = $this->createComponentMock([$component1]);
        $component2 = $this->createComponentMock([$component3]);

        // create circular requirement
        $component1->expects($this->any())
            ->method('requires')
            ->willReturn([$component2]);

        $components[] = $component1;
        $components[] = $component2;
        $components[] = $component3;

        $solver = new DependencySolver();

        $solver->solve($components);
    }

    /**
     * @param array $requires
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|ComponentInterface
     */
    protected function createComponentMock($requires = [])
    {
        $component = $this->getMockBuilder(ComponentInterface::class)
            ->getMockForAbstractClass();

        if (false !== $requires) {
            $component->expects($this->any())
                ->method('requires')
                ->willReturn($requires);
        }

        return $component;
    }
}
