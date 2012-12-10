/**
* This class lets you generate zip files in AS3
* AS3 implementation of the following PHP script :
* http://www.zend.com/zend/spotlight/creating-zip-files1.php?article=creating-zip-files1&kind=sl&id=274&open=1&anc=0&view=1
* 
* @author Thibault Imbert (bytearray.org)
* @usage Compression methods for the files : 
* 
* CompressionMethod.NONE = no compression is applied
* CompressionMethod.GZIP = native GZIP compression is applied
* 
* first parameter : compression method
* 
* var myZip:ASZIP = new ASZIP ( CompressionMethod.GZIP );
* 
* @version 0.1 First release
* @version 0.2 ASZip.saveZIP method added
*/

package org.aszip.zip 
{
	
	import flash.accessibility.Accessibility;
	import flash.utils.ByteArray;
	import flash.utils.Endian;
	import flash.net.URLRequest;
	import flash.net.URLRequestHeader;
	import flash.net.URLRequestMethod;
	import flash.net.navigateToURL;
	import org.aszip.saving.Method;
	import org.aszip.compression.CompressionMethod;
	import org.aszip.crc.CRC32;

	/**
	* The ASZip class represents a Zip file
	*/
	public class ASZip 
	
	{
		
		/**
		* The compressed data buffer
		*/	
		private var compressedData:ByteArray;
		/**
		* The central directory
		*/	
		private var centralDirectory:ByteArray;
		/**
		* The central index
		*/	
		private var oldOffset:Number
		/**
		* Number of directories in the zip
		*/	
		private var nbDirectory:Array;
		/**
		* The final zip stream
		*/	
		private var output:ByteArray;
		/**
		* The compression method used
		*/	
		private var compressionMethod:String;
		/**
		* The comment string
		*/	
		private var comment:String;
		
		/**
		* Lets you create a Zip file
		* 
		* @param pCompression Compression method
		* @example
		* This example shows how to create a valid ZIP file :
		* <div class="listing">
		* <pre>
		* 
		* var myZip:ASZip = new ASZip ( CompressionMethod.GZIP );
		* </pre>
		* </div>
		*/
		public function ASZip ( pCompression:String='GZIP' )
		
		{
			
			compressedData = new ByteArray;
			centralDirectory = new ByteArray;
			output = new ByteArray;
			nbDirectory = new Array
			comment = new String;;
			oldOffset = 0;
			compressionMethod = pCompression;
			
		}
		
		/**
		* Lets you create a directory for the current Zip
		* 
		* @param directoryName Name of the directory
		* @example
		* This example shows how to create a directory and subdirectory :
		* <div class="listing">
		* <pre>
		* 
		* myZip.addDirectory ( "images" );
		* myZip.addDirectory ( "images/funk" );
		* </pre>
		* </div>
		*/	
		public function addDirectory ( directoryName:String ):void 
		
		{
			
			directoryName = directoryName.split ('\\').join ('/');  

			var feedArrayRow:ByteArray = new ByteArray;
			feedArrayRow.endian = Endian.LITTLE_ENDIAN;
			feedArrayRow.writeUnsignedInt ( 0x04034b50 );
			feedArrayRow.writeShort ( 0x000a );
			feedArrayRow.writeShort ( 0x0000 );
			feedArrayRow.writeShort ( 0x0000 );
			feedArrayRow.writeUnsignedInt ( unixToDos ( new Date().getTime() ) ); 

			feedArrayRow.writeUnsignedInt (0); 
			feedArrayRow.writeUnsignedInt (0); 
			feedArrayRow.writeUnsignedInt (0); 
			feedArrayRow.writeShort ( directoryName.length ); 
			feedArrayRow.writeShort ( 0 ); 
			feedArrayRow.writeUTFBytes ( directoryName );  

			feedArrayRow.writeUnsignedInt ( 0 ); 
			feedArrayRow.writeUnsignedInt ( 0 ); 
			feedArrayRow.writeUnsignedInt ( 0 ); 

			compressedData.writeBytes ( feedArrayRow );
			
			var newOffset:int = this.compressedData.length;

			// Directory header
			var addCentralRecord:ByteArray = new ByteArray;
			addCentralRecord.endian = Endian.LITTLE_ENDIAN;
			addCentralRecord.writeUnsignedInt ( 0x02014b50 );
			addCentralRecord.writeShort ( 0x0000 );    
			addCentralRecord.writeShort ( 0x000a );   
			addCentralRecord.writeShort ( 0x0000 );   
			addCentralRecord.writeShort ( 0x0000 );    
			addCentralRecord.writeUnsignedInt ( 0x00000000 ); 
			addCentralRecord.writeUnsignedInt ( 0 ); 
			addCentralRecord.writeUnsignedInt ( 0 ); 
			addCentralRecord.writeUnsignedInt ( 0 ); 
			addCentralRecord.writeShort ( directoryName.length ); 
			addCentralRecord.writeShort ( 0 ); 
			addCentralRecord.writeShort ( 0 ); 
			addCentralRecord.writeShort ( 0 ); 
			addCentralRecord.writeShort ( 0 ); 
			addCentralRecord.writeUnsignedInt ( 16 ); 
			addCentralRecord.writeUnsignedInt ( this.oldOffset ); 
			
			this.oldOffset = newOffset;

			addCentralRecord.writeUTFBytes (directoryName);

			this.nbDirectory.push ( addCentralRecord );
			this.centralDirectory.writeBytes ( addCentralRecord );
			
		}
		
		/**
		* Lets you add a file into a specific directory
		* 
		* @param pBytes File stream
		* @param pDirectory Directory name
		* @example
		* This example shows how to add files into directories :
		* <div class="listing">
		* <pre>
		* 
		* myZip.addFile ( imageByteArray, "images/image.jpg" );
		* myZip.addFile ( imageByteArray, "images/funk/image.jpg" );
		* </pre>
		* </div>
		*/	
		public function addFile ( pBytes:ByteArray, pDirectory:String ):void
		
		{
			
			pDirectory = pDirectory.split ('\\').join ('/');
			
			var feedArrayRow:ByteArray = new ByteArray;
			feedArrayRow.endian = Endian.LITTLE_ENDIAN;
			
			// Local File Header
			feedArrayRow.writeUnsignedInt ( 0x04034b50 );
			feedArrayRow.writeShort ( 0x0014 );
			feedArrayRow.writeShort ( 0x0000 );
			
			// File is deflated 
			feedArrayRow.writeShort ( this.compressionMethod == CompressionMethod.GZIP ? 0x0008 : 0x0000 );
			
			feedArrayRow.writeUnsignedInt ( unixToDos ( new Date().getTime() ) ); 

			var uncompressedLength:Number = pBytes.length; 
			
			// CRC32 checksum 
			var crc:CRC32 = new CRC32;
			crc.generateCRC32 ( pBytes );
			var compression:int = crc.getCRC32();
			
			// If GZIP compression
			if ( compressionMethod == CompressionMethod.GZIP ) 
			
			{
				
				pBytes.compress();
				var copy:ByteArray = new ByteArray;
				copy.writeBytes ( pBytes, 0, pBytes.length - 4 );
				var finalCopy:ByteArray = new ByteArray;
				finalCopy.writeBytes ( copy, 2 );
				pBytes = finalCopy;
				
			}
			
			var compressedLength:int = pBytes.length;
			
			feedArrayRow.writeUnsignedInt ( compression );
			feedArrayRow.writeUnsignedInt ( compressedLength );
			feedArrayRow.writeUnsignedInt ( uncompressedLength ); 
			feedArrayRow.writeShort ( pDirectory.length ); 
			feedArrayRow.writeShort ( 0 ); 
			feedArrayRow.writeUTFBytes ( pDirectory ); 
			feedArrayRow.writeBytes ( pBytes );  
			
			// Data Descriptor
			feedArrayRow.writeUnsignedInt ( compression ); 
			feedArrayRow.writeUnsignedInt ( compressedLength ); 
			feedArrayRow.writeUnsignedInt ( uncompressedLength );

			compressedData.writeBytes ( feedArrayRow );

			var newOffset:int = compressedData.length;

			// File header
			var addCentralRecord:ByteArray = new ByteArray;
			addCentralRecord.endian = Endian.LITTLE_ENDIAN;
			addCentralRecord.writeUnsignedInt ( 0x02014b50 );
			addCentralRecord.writeShort ( 0x0000 );    
			addCentralRecord.writeShort ( 0x0014 );    
			addCentralRecord.writeShort ( 0x0000 );    
			addCentralRecord.writeShort ( this.compressionMethod == CompressionMethod.GZIP ? 0x0008 : 0x0000 );    
			addCentralRecord.writeUnsignedInt ( unixToDos ( new Date().getTime() ) ); 
			addCentralRecord.writeUnsignedInt ( compression ); 
			addCentralRecord.writeUnsignedInt ( compressedLength ); 
			addCentralRecord.writeUnsignedInt ( uncompressedLength ); 
			addCentralRecord.writeShort(pDirectory.length); 
			addCentralRecord.writeShort ( 0 );
			addCentralRecord.writeShort ( 0 );
			addCentralRecord.writeShort ( 0 );
			addCentralRecord.writeShort ( 0 );
			addCentralRecord.writeUnsignedInt( 32 ); 

			addCentralRecord.writeUnsignedInt ( this.oldOffset ); 
			this.oldOffset = newOffset;

			addCentralRecord.writeUTFBytes  (pDirectory);  

			this.nbDirectory.push ( addCentralRecord );
			this.centralDirectory.writeBytes ( addCentralRecord );
			
		}
		
		/**
		* Lets you add a comment into the Zip file
		* 
		* @param pComment The comment string to add
		* @example
		* This example shows how to add a comment for the current zip :
		* <div class="listing"><pre>myZip.addComment ( "Hello there !");</pre></div>
		*/
		public function addComment ( pComment:String ):void
		
		{
			
			comment = pComment;
			
		}
		
		/**
		* Lets you finalize and save the ZIP file and make it available for download
		* 
		* @param pMethod Can be se to Method.LOCAL, the saveZIP will return the ZIP ByteArray. When Method.REMOTE is passed, just specify the path to the create.php file
		* @param pURL The url of the create.php file
		* @param pDownload Lets you specify the way the ZIP is going to be available. Use Download.INLINE if you want the ZIP to be directly opened, use Download.ATTACHMENT if you want to make it available with a save-as dialog box
		* @param pName The name of the ZIP, only available when Method.REMOTE is used
		* @return The ByteArray ZIP when Method.LOCAL is used, otherwise the method returns null
		* @example
		* This example shows how to save the ZIP with a download dialog-box :
		* <div class="listing">
		* <pre>
		* 
		* myZIP.saveZIP ( Method.REMOTE, 'create.php', Download.ATTACHMENT, 'archive.zip' );
		* </pre>
		* </div>
		*/	
		public function saveZIP ( pMethod:String, pURL:String='', pDownload:String='inline', pName:String='archive.zip' ):*
		
		{
			
			output = new ByteArray;
			output.endian = Endian.LITTLE_ENDIAN;
			output.writeBytes ( this.compressedData );
			output.writeBytes ( this.centralDirectory );
			output.writeUnsignedInt ( 0x06054b50 );
			output.writeUnsignedInt ( 0x00000000 );
			output.writeShort ( this.nbDirectory.length );
			output.writeShort ( this.nbDirectory.length );
			output.writeUnsignedInt ( this.centralDirectory.length );
			output.writeUnsignedInt ( this.compressedData.length );
			output.writeShort ( comment.length );
			writeUTFChars ( comment );
			
			if ( pMethod == Method.LOCAL ) return output;
			
			var header:URLRequestHeader = new URLRequestHeader ("Content-type", "application/octet-stream");
			var myRequest:URLRequest = new URLRequest ( pURL+'?name='+pName+'&method='+pDownload );
			myRequest.requestHeaders.push (header);
			myRequest.method = URLRequestMethod.POST;
			myRequest.data = saveZIP ( Method.LOCAL );
			
			navigateToURL ( myRequest, "_blank" );
			
			return null;
			
		}
		
		/*
		* 
		* PRIVATE MEMBERS
		* 
		*/
		
		private function writeUTFChars ( pString:String ):void 
		
		{
			
			var lng:int = pString.length;
	
			for (var i:int = 0; i<lng; i++ ) output.writeByte( pString.charCodeAt ( i ) );
			
		}
	
		private function unixToDos( pTimeStamp:Number ):Number
		{
			
			  var currentDate:Date  = new Date ( pTimeStamp );
			  
			  if ( currentDate.getFullYear() < 1980 ) currentDate = new Date (1980, 1, 1, 0, 0, 0);
			  
			  return ( (currentDate.getFullYear() - 1980) << 25) | (currentDate.getMonth() << 21) | (currentDate.getDate() << 16) | 
					   (currentDate.getHours() << 11) | (currentDate.getMinutes() << 5) | (currentDate.getSeconds() >> 1);
						
		}
		
	}
	
}