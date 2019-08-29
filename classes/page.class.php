<?




class Page extends Layout {

   private $oHeader;
   private $oBody;
   private $oFooter;

   public function __Construct() {
      parent::__Construct();
   }

   public function SetHeader($oHeader) {
      $this->oHeader = $oHeader;
   }

   public function SetBody($oBody) {
      $this->oBody = $oBody;
   }

   public function SetFooter($oFooter) {
      $this->oFooter = $oFooter;
   }

   public function GetContent() {
      
      $out = (is_object($this->oHeader)) ? $this->oHeader->Render() : "";
      $out .= (is_object($this->oBody)) ? $this->oBody->Render() : "";
      $out .= (is_object($this->oFooter)) ? $this->oFooter->Render() : "";

      return $out;
   }

}




?>