<?php
namespace SbxCommon\View\Helper\Navigation;

use Zend\View\Helper\Navigation\Menu as ZendMenu;
use RecursiveIteratorIterator;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\View;
use Zend\View\Exception;

class Menu extends ZendMenu
{
    /**
     * Renders a normal menu (called from {@link renderMenu()})
     *
     * @param  AbstractContainer $container          container to render
     * @param  string            $ulClass            CSS class for first UL
     * @param  string            $indent             initial indentation
     * @param  int|null          $minDepth           minimum depth
     * @param  int|null          $maxDepth           maximum depth
     * @param  bool              $onlyActive         render only active branch?
     * @param  bool              $escapeLabels       Whether or not to escape the labels
     * @param  bool              $addClassToListItem Whether or not page class applied to <li> element
     * @param  string            $liActiveClass      CSS class for active LI
     * @return string
     */
    protected function renderNormalMenu(
        AbstractContainer $container,
        $ulClass,
        $indent,
        $minDepth,
        $maxDepth,
        $onlyActive,
        $escapeLabels,
        $addClassToListItem,
        $liActiveClass
    ) {
        $html = '';

        // find deepest active
        $found = $this->findActive($container, $minDepth, $maxDepth);
        /* @var $escaper \Zend\View\Helper\EscapeHtmlAttr */
        $escaper = $this->view->plugin('escapeHtmlAttr');

        if ($found) {
            $foundPage  = $found['page'];
            $foundDepth = $found['depth'];
        } else {
            $foundPage = null;
        }

        // create iterator
        $iterator = new RecursiveIteratorIterator($container,
            RecursiveIteratorIterator::SELF_FIRST);
        if (is_int($maxDepth)) {
            $iterator->setMaxDepth($maxDepth);
        }

        // iterate container
        $prevDepth = -1;
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $isActive = $page->isActive(true);
            if ($depth < $minDepth || !$this->accept($page)) {
                // page is below minDepth or not accepted by acl/visibility
                continue;
            } elseif ($onlyActive && !$isActive) {
                // page is not active itself, but might be in the active branch
                $accept = false;
                if ($foundPage) {
                    if ($foundPage->hasPage($page)) {
                        // accept if page is a direct child of the active page
                        $accept = true;
                    } elseif ($foundPage->getParent()->hasPage($page)) {
                        // page is a sibling of the active page...
                        if (!$foundPage->hasPages(!$this->renderInvisible) ||
                            is_int($maxDepth) && $foundDepth + 1 > $maxDepth) {
                            // accept if active page has no children, or the
                            // children are too deep to be rendered
                            $accept = true;
                        }
                    }
                }

                if (!$accept) {
                    continue;
                }
            }

            // make sure indentation is correct
            $depth -= $minDepth;
            $myIndent = $indent . str_repeat('        ', $depth);

            if ($depth > $prevDepth) {
                // start new ul tag
                if ($ulClass && $depth ==  0) {
                    $ulClassString = ' class="' . $escaper($ulClass) . '"';

                }elseif($depth > 0){
                    $ulClassString = ' class="dropdown-menu" role="menu"';
                } else {
                    $ulClassString = '';
                }

                $html .= $myIndent . '<ul' . $ulClassString . '>' . PHP_EOL;
            } elseif ($prevDepth > $depth) {
                // close li/ul tags until we're at current depth
                for ($i = $prevDepth; $i > $depth; $i--) {
                    $ind = $indent . str_repeat('        ', $i);
                    $html .= $ind . '    </li>' . PHP_EOL;
                    $html .= $ind . '</ul>' . PHP_EOL;
                }
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            } else {
                // close previous li tag
                $html .= $myIndent . '    </li>' . PHP_EOL;
            }

            // render li tag and page
            $liClasses = array();
            // Is page active?
            if ($isActive) {
                $liClasses[] = $liActiveClass;
            }
            // Add CSS class from page to <li>
            if ($addClassToListItem && $page->getClass()) {
                $liClasses[] = $page->getClass();
            }
            if(!empty($page->getPages())){
                $liClasses[] = 'dropdown';
            }

            if($page instanceof \SbxCommon\Navigation\Page\Divider){
                $liClasses[] = 'divider';
            }
            $liClass = empty($liClasses) ? '' : ' class="' . $escaper(implode(' ', $liClasses)) . '"';

            $pageHtml = $this->htmlify($page, $escapeLabels, $addClassToListItem);
            $html .= $myIndent . '    <li' . $liClass . '>' . PHP_EOL
                . $myIndent . '        ' . $pageHtml . PHP_EOL;

            // store as previous depth for next iteration
            $prevDepth = $depth;
        }

        if ($html) {
            // done iterating container; close open ul/li tags
            for ($i = $prevDepth+1; $i > 0; $i--) {
                $myIndent = $indent . str_repeat('        ', $i-1);
                $html .= $myIndent . '    </li>' . PHP_EOL
                    . $myIndent . '</ul>' . PHP_EOL;
            }
            $html = rtrim($html, PHP_EOL);
        }

        return $html;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page if
     * the page's href is not empty, and a 'span' element if it is empty
     *
     * Overrides {@link AbstractHelper::htmlify()}.
     *
     * @param  AbstractPage $page               page to generate HTML for
     * @param  bool         $escapeLabel        Whether or not to escape the label
     * @param  bool         $addClassToListItem Whether or not to add the page class to the list item
     * @return string
     */
    public function htmlify(AbstractPage $page, $escapeLabel = true, $addClassToListItem = false)
    {
        // get attribs for element
        $attribs = array(
            'id'     => $page->getId(),
            'title'  => $this->translate($page->getTitle(), $page->getTextDomain()),
        );
        $labelAddon = '';
        if ($addClassToListItem === false) {
            $attribs['class'] = $page->getClass();
        }
        if(!empty($page->getPages())){
            $attribs['class'] = !empty($attribs['class']) ? $attribs['class'] . ' dropdown-toggle' : 'dropdown-toggle';
            $attribs['aria-expanded'] = 'false';
            $attribs['role'] = 'button';
            $attribs['data-toggle'] = 'dropdown';
            $labelAddon = ' <span class="caret"></span>';
        }
        // does page have a href?
        $href = $page->getHref();
        if ($href) {
            $element = 'a';
            $attribs['href'] = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        $html  = '<' . $element . $this->htmlAttribs($attribs) . '>';
        $label = $this->translate($page->getLabel(), $page->getTextDomain());
        if ($escapeLabel === true) {
            /** @var \Zend\View\Helper\EscapeHtml $escaper */
            $escaper = $this->view->plugin('escapeHtml');
            $html .= $escaper($label);
        } else {
            $html .= $label;
        }
        $html .= $labelAddon . '</' . $element . '>';

        return $html;
    }
}