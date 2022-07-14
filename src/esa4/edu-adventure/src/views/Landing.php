<?php

namespace EduAdventure\Views;

use EduAdventure\Utils\HtmlHelper;

require_once __DIR__ . '/../../vendor/autoload.php';
class Landing
{
    static function getPage()
    {
        $content = '<h1 class="mt-5">Edu Adventure</h1>
        <p class="lead">A company that can streamline elegantly will (at some undefined point of time in the future) be able to engineer seamlessly. Quick: do you have a infinitely reconfigurable scheme for coping with emerging methodologies? Is it more important for something to be dynamic or to be customer-directed? What does the industry jargon "C2B2B" really mean? What do we transform? Anything and everything, regardless of incomprehensibility! We understand that it is better to e-enable intuitively than to morph intuitively. Clicking on this link which refers to B2B Marketing awards shortlist will take you to the capacity to enable perfectly leads to the capacity to synthesize interactively. Our functionality is unparalleled, but our back-end performance and non-complex use is invariably considered a remarkable achievement taking into account this month\'s financial state of things! If all of this may seem terrific, but it\'s 100% realistic! What does the term "dot-com" really mean? Without macro-vertical CAE, you will lack architectures. We believe we know that if you incentivize dynamically, you may have to synergize wirelessly. Do you have a virally-distributed plan of action for managing emerging partnerships? Without data hygiene supervising, you will lack social networks. Without micro-resource-constrained performance, you will lack architectures. Is it more important for something to be best-of-breed? The portals factor can be delivered as-a-service to wherever it\’s intended to go – mobile.
</p>';
        $page = HtmlHelper::MakePage($content);
        echo $page;
    }
}