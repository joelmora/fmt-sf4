<?php
namespace isoft\fmtsf4\Helpers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

trait LanguageTrait
{
    /**
     * Check if language header is available and set locale
     * @param Request $request
     * @param TranslatorInterface $translator
     */
    public function checkLanguage(Request &$request, TranslatorInterface $translator)
    {
        $validLanguages = ['ES', 'EN'];
        $lang = strtolower($request->headers->get('accept-language'));

        if (in_array(strtoupper($lang), $validLanguages)) {
            $translator->setLocale($lang);
            $request->setLocale($lang);
        }
    }
}
