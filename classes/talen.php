<?
class Languages extends BigTreeModule {
    var $Table = "languages";

    public function getLanguage(){
        global $bigtree;
        $languageId = $bigtree['page']['resources']['taal'];
        if ($languageId == '') {
            return '';
        }
        $languageProperties = $this->get($languageId);
        return $languageProperties;
    }
}
?>