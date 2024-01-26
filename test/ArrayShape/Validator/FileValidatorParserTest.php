<?php

declare(strict_types=1);

namespace KynxTest\Laminas\FormCli\ArrayShape\Validator;

use Kynx\Laminas\FormCli\ArrayShape\Type\AbstractParsedType;
use Kynx\Laminas\FormCli\ArrayShape\Type\ClassString;
use Kynx\Laminas\FormCli\ArrayShape\Type\Generic;
use Kynx\Laminas\FormCli\ArrayShape\Type\PsalmType;
use Kynx\Laminas\FormCli\ArrayShape\Validator\FileValidatorParser;
use Laminas\Validator\Barcode;
use Laminas\Validator\File\Crc32;
use Laminas\Validator\File\ExcludeMimeType;
use Laminas\Validator\File\Exists;
use Laminas\Validator\File\FilesSize;
use Laminas\Validator\File\Hash;
use Laminas\Validator\File\ImageSize;
use Laminas\Validator\File\IsCompressed;
use Laminas\Validator\File\IsImage;
use Laminas\Validator\File\Md5;
use Laminas\Validator\File\MimeType;
use Laminas\Validator\File\Sha1;
use Laminas\Validator\File\Size;
use Laminas\Validator\File\UploadFile;
use Laminas\Validator\File\WordCount;
use Laminas\Validator\ValidatorInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

/**
 * @psalm-import-type ParsedArray from AbstractParsedType
 */
#[CoversClass(FileValidatorParser::class)]
final class FileValidatorParserTest extends TestCase
{
    /**
     * @param ParsedArray $existing
     */
    #[DataProvider('getTypesProvider')]
    public function testGetTypes(ValidatorInterface $validator, array $existing, array $expected): void
    {
        $parser = new FileValidatorParser();
        $actual = $parser->getTypes($validator, $existing);
        self::assertEquals($expected, $actual);
    }

    public static function getTypesProvider(): array
    {
        $uploadFile = new ClassString(UploadedFileInterface::class);

        // phpcs:disable Generic.Files.LineLength.TooLong
        return [
            'invalid'       => [new Barcode(), [PsalmType::Bool], [PsalmType::Bool]],
            'array'         => [new Crc32(), [PsalmType::Array], [new Generic(PsalmType::NonEmptyArray, [PsalmType::NonEmptyString])]],
            'string'        => [new Crc32(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'exclude mime'  => [new ExcludeMimeType(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'exists'        => [new Exists(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'file size'     => [new FilesSize(['max' => 123]), [PsalmType::String], [PsalmType::NonEmptyString]],
            'hash'          => [new Hash(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'image size'    => [new ImageSize(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'is compressed' => [new IsCompressed(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'is image'      => [new IsImage(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'md5'           => [new Md5(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'mime type'     => [new MimeType(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'sha1'          => [new Sha1(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'size'          => [new Size(), [PsalmType::String], [PsalmType::NonEmptyString]],
            'upload file'   => [new UploadFile(), [$uploadFile], [$uploadFile]],
            'word count'    => [new WordCount(), [PsalmType::String], [PsalmType::NonEmptyString]],
        ];
        // phpcs:enable
    }
}
