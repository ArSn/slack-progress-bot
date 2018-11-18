<?php

use App\Models\Step;

class StepTest extends TestCase
{
	public function testNameMutator()
	{
		$name = 'some-random-test-name';

		$step = new Step();
		$this->assertEmpty($step->getName());
		$step->setName($name);
		$this->assertSame($name, $step->getName());
	}

	public function testGetType()
	{
		$this->assertSame('App\Models\Step', (new Step)->getType());
	}

	public function stepRepresentationDataProvider()
	{
		return [
			// name, state, result
			['some-bla', Step::STATE_NEW, '{"name":"some-bla","state":"new"}'],
			['some-nameefef3', Step::STATE_IN_PROGRESS, '{"name":"some-nameefef3","state":"in-progress"}'],
			['success', Step::STATE_SUCCESSFUL, '{"name":"success","state":"successful"}'],
			['some-namee222222222', Step::STATE_FAILED, '{"name":"some-namee222222222","state":"failed"}'],
		];
	}

	/**
	 * @dataProvider stepRepresentationDataProvider
	 */
	public function testToStringRepresentsStep($name, $state, $result)
	{
		$step = new Step();
		$step->setName($name);
		$step->setState($state);

		$this->assertSame($result, $step->toString());
	}

	/**
	 * @dataProvider stepRepresentationDataProvider
	 */
	public function testFromStringGeneratesProperModel($name, $state, $result)
	{
		$step = new Step();
		$step->fromString($result);

		$this->assertSame($name, $step->getName());
		$this->assertSame($state, $step->getState());
	}



	public function invalidStatesProvider()
	{
		return [
			['hello'],
			['yes yes'],
			['this-is-no-state'],
			['may-the-4th-be-withu'],
		];
	}

	/**
	 * @expectedException DomainException
	 * @dataProvider invalidStatesProvider
	 */
	public function testInvalidStateThrowsDomainException($invalidState)
	{
		$step = new Step();
		$this->setExpectedExceptionFromAnnotation();
		$step->setState($invalidState);
	}

	public function testMarkingInProgress()
	{
		$step = new Step();
		$step->markInProgress();
		$this->assertSame(Step::STATE_IN_PROGRESS, $step->getState());
	}

	public function testMarkingSuccessful()
	{
		$step = new Step();
		$step->markSuccessful();
		$this->assertSame(Step::STATE_SUCCESSFUL, $step->getState());
	}

	public function testMarkingFailed()
	{
		$step = new Step();
		$step->markFailed();
		$this->assertSame(Step::STATE_FAILED, $step->getState());
	}

	public function composedMessageDataProvider()
	{
		return [
			// name, state, message
			['effe1', Step::STATE_NEW, 'effe1 [...]'],
			['effe2', Step::STATE_IN_PROGRESS, 'effe2 [:hourglass_flowing_sand:]'],
			['effe3', Step::STATE_SUCCESSFUL, 'effe3 [:heavy_check_mark:]'],
			['effe4', Step::STATE_FAILED, 'effe4 [:x:]'],
		];
	}

	/**
	 * @dataProvider composedMessageDataProvider
	 */
	public function testComposingMessages($name, $state, $message)
	{
		$step = new Step();
		$step->setName($name);
		$step->setState($state);
		$this->assertSame($message, $step->composeStepMessage());
	}

	/**
	 * @expectedException  RuntimeException
	 */
	public function testComposingMessageWithoutStateThrowsExceptionOnMessageComposing()
	{
		$class = new ReflectionClass(Step::class);
		$property = $class->getProperty("state");
		$property->setAccessible(true);
		$step = new Step();
		$property->setValue($step, 'total-nonsense');

		$this->setExpectedExceptionFromAnnotation();
		$step->composeStepMessage();
	}
}
