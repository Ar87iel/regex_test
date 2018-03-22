<?php
namespace EMRAdminTest\unit\tests\Service\User\Dto;

use EMRAdmin\Service\User\Dto\Type;
use PHPUnit_Framework_TestCase;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Type
     */
    private $type;

    public function setUp()
    {
        $this->type = new Type;
    }

    public function testPt()
    {
        $this->type->setDescription(Type::PHYSICAL_THERAPIST);
        $this->assertTrue($this->type->isPhysicalTherapist());
    }

    public function testSpt()
    {
        $this->type->setDescription(Type::PHYSICAL_THERAPIST_STUDENT);
        $this->assertTrue($this->type->isPhysicalTherapistStudent());
    }

    public function testPta()
    {
        $this->type->setDescription(Type::PHYSICAL_THERAPIST_ASSISTANT);
        $this->assertTrue($this->type->isPhysicalTherapistAssistant());
    }

    public function testClerical()
    {
        $this->type->setDescription(Type::CLERICAL);
        $this->assertTrue($this->type->isClerical());
    }

    public function testSuperUser()
    {
        $this->type->setDescription(Type::SUPER_USER);
        $this->assertTrue($this->type->isSuperUser());
    }

    public function testOt()
    {
        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST);
        $this->assertTrue($this->type->isOccupationalTherapist());
    }

    public function testCota()
    {
        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST_ASSISTANT);
        $this->assertTrue($this->type->isOccupationalTherapistAssistant());
    }

    public function testSot()
    {
        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST_STUDENT);
        $this->assertTrue($this->type->isOccupationalTherapistStudent());
    }

    public function testRt()
    {
        $this->type->setDescription(Type::RESPIRATORY_THERAPIST);
        $this->assertTrue($this->type->isRespiratoryTherapist());
    }

    public function testRtt()
    {
        $this->type->setDescription(Type::RESPIRATORY_THERAPIST_TECHNICIAN);
        $this->assertTrue($this->type->isRespiratoryTherapistTechnician());
    }

    public function testDc()
    {
        $this->type->setDescription(Type::CHIROPRACTIC_DOCTOR);
        $this->assertTrue($this->type->isChirorpracticDoctor());
    }

    public function testSlp()
    {
        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST);
        $this->assertTrue($this->type->isSpeechLanguagePathologist());
    }

    public function testSlpa()
    {
        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST_ASSISTANT);
        $this->assertTrue($this->type->isSpeechLanguagePathologistAssistant());
    }

    public function testSslp()
    {
        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST_STUDENT);
        $this->assertTrue($this->type->isSpeechLanguagePathologistStudent());
    }

    public function testAtc()
    {
        $this->type->setDescription(Type::ATHLETIC_TRAINER);
        $this->assertTrue($this->type->isAthleticTrainer());
    }

    public function testIsStudent()
    {
        $this->type->setDescription(Type::PHYSICAL_THERAPIST);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::PHYSICAL_THERAPIST_ASSISTANT);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::CLERICAL);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::SUPER_USER);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST_ASSISTANT);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::PHYSICAL_THERAPIST_STUDENT);
        $this->assertTrue($this->type->isStudent());

        $this->type->setDescription(Type::RESPIRATORY_THERAPIST);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::CHIROPRACTIC_DOCTOR);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::ATHLETIC_TRAINER);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST_ASSISTANT);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::RESPIRATORY_THERAPIST_TECHNICIAN);
        $this->assertFalse($this->type->isStudent());

        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST_STUDENT);
        $this->assertTrue($this->type->isStudent());

        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST_STUDENT);
        $this->assertTrue($this->type->isStudent());

    }

    public function testIsAssistant()
    {
        $this->type->setDescription(Type::PHYSICAL_THERAPIST);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::PHYSICAL_THERAPIST_ASSISTANT);
        $this->assertTrue($this->type->isAssistant());

        $this->type->setDescription(Type::CLERICAL);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::SUPER_USER);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST_ASSISTANT);
        $this->assertTrue($this->type->isAssistant());

        $this->type->setDescription(Type::PHYSICAL_THERAPIST_STUDENT);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::RESPIRATORY_THERAPIST);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::CHIROPRACTIC_DOCTOR);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::ATHLETIC_TRAINER);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST_ASSISTANT);
        $this->assertTrue($this->type->isAssistant());

        $this->type->setDescription(Type::RESPIRATORY_THERAPIST_TECHNICIAN);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::OCCUPATIONAL_THERAPIST_STUDENT);
        $this->assertFalse($this->type->isAssistant());

        $this->type->setDescription(Type::SPEECH_LANGUAGE_PATHOLOGIST_STUDENT);
        $this->assertFalse($this->type->isAssistant());

    }
}