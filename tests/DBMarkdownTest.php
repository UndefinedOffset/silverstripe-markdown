<?php

namespace UndefinedOffset\Markdown\Test;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\ORM\FieldType\DBField;
use UndefinedOffset\Markdown\Model\FieldTypes\DBMarkdown;

/**
 * Class DBMarkdownTest
 * @package UndefinedOffset\Markdown\Test
 */
class DBMarkdownTest extends SapphireTest
{
    /**
     *
     */
    public function testSetCacheKey()
    {
        /** @var DBMarkdown $field */
        $field = DBField::create_field(DBMarkdown::class, '###Headline', 'Markdown');
        $this->assertEquals('9ec6af9103bea4520f8f7a2300c23a3b', $field->getCacheKey());

        $field->setCacheKey('foo');
        $this->assertEquals('foo', $field->getCacheKey());
    }

    /**
     *
     */
    public function testKeySeconds()
    {
        $this->assertEquals(86400, DBMarkdown::config()->get('cache_seconds'));

        DBMarkdown::config()->set('cache_seconds', 500);
        $this->assertEquals(500, DBMarkdown::config()->get('cache_seconds'));
    }
}
