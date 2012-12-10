/**
* AS3 CRC32 algorithm implementation
*/

package org.aszip.crc
{
	
	import flash.utils.ByteArray;
	
    public class CRC32 {
		
        private var crc32:int;
        private static var CRCTable:Array = initLookupTable();
		
        private static function initLookupTable ():Array
		{
			
			var polynomial:int = 0xEDB88320;
            var CRC32Table:Array = new Array(256);
			
			var i:int = 256;
			var j:int = 8;
			
			while ( i-- )
			
			{
				
				var crc:int = i;

				while ( j-- ) crc = (crc & 1) ? (crc >>> 1) ^ polynomial : (crc >>> 1);
				
				j = 8;
				
				CRC32Table [ i ] = crc;
				
			}
			
			return CRC32Table;
			
        }

        public function generateCRC32 ( pBytes:ByteArray ):void
		{
			
            var length:int = pBytes.length;
			
            var crc:int = ~crc32;
			
			for ( var i:int = 0; i < length; i++ ) crc = ( crc  >>> 8 ) ^ CRCTable[ pBytes[i] ^ (crc & 0xFF) ];
			
            crc32 = ~crc;
			
        }

        public function getCRC32 ():int
		{
			
            return crc32 & 0xFFFFFFFF;
			
        }
		
    }
}