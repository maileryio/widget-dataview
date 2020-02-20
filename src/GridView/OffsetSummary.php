<?php

namespace Mailery\Dataview\GridView;

use App\Factory\I18nFactory;
use Mailery\Dataview\Paginator\OffsetPaginator;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\I18n\MessageFormatterInterface;
use Yiisoft\I18n\TranslatorInterface;
use Yiisoft\Html\Html;
use Yiisoft\Widget\Widget;

class OffsetSummary extends Widget
{

    /**
     * @var string|null
     */
    private $summary;

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @var OffsetPaginator
     */
    private OffsetPaginator $paginator;

    /**
     *
     * @var type @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var MessageFormatterInterface
     */
    private MessageFormatterInterface $formatter;

    /**
     * @param TranslatorInterface $translator
     * @param MessageFormatterInterface $formatter
     */
    public function __construct(TranslatorInterface $translator, MessageFormatterInterface $formatter)
    {
        $this->translator = $translator;
        $this->formatter = $formatter;
    }

    /**
     * @param string $summary
     * @return $this
     */
    public function summary(string $summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param OffsetPaginator $paginator
     * @return $this
     */
    public function paginator(OffsetPaginator $paginator)
    {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render(): string
    {
        $totalCount = $this->paginator->getTotalCount();
        if ($totalCount <= 0) {
            return '';
        }

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');

        if ($this->paginator->getTotalPages() > 1) {
            $page = $this->paginator->getCurrentPage();
            $begin = ($page - 1) * $this->paginator->getPageSize() + 1;
            $pageCount = $this->paginator->getTotalPages();
            $end = $begin + $this->paginator->getCurrentPageSize() - 1;

            if ($begin > $end) {
                $begin = $end;
            }

            if (($content = $this->summary) === null) {
                $content = $this->translator->translate(
                    'Showing {begin, number} to {end, number} of {totalCount, number} {totalCount, plural, one{item} other{items}}',
                    [
                        'begin'      => $begin,
                        'end'        => $end,
                        'totalCount' => $totalCount,
                        'page'       => $page,
                        'pageCount'  => $pageCount,
                    ],
                    'dataview'
                );
                return Html::tag($tag, $content, $options);
            }
        } else {
            $page = $begin = $pageCount = 1;
            $end = $totalCount;

            if (($content = $this->summary) === null) {
                $content = $this->translator->translate(
                    'Total {totalCount, number} {totalCount, plural, one{item} other{items}}',
                    [
                        'begin'      => $begin,
                        'end'        => $end,
                        'totalCount' => $totalCount,
                        'page'       => $page,
                        'pageCount'  => $pageCount,
                    ],
                    'dataview'
                );
                return Html::tag($tag, $content, $options);
            }
        }

        return $this->formatter->format(
            $content,
            [
                'begin'      => $begin,
                'end'        => $end,
                'totalCount' => $totalCount,
                'page'       => $page,
                'pageCount'  => $pageCount,
            ]
        );
    }

}
