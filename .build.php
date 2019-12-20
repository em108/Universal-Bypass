<?php
if(file_exists("Universal Bypass.zip"))
{
	unlink("Universal Bypass.zip");
}
if(file_exists("Universal Bypass for Firefox.zip"))
{
	unlink("Universal Bypass for Firefox.zip");
}

echo "Indexing...\n";
$index = [];
function recursivelyIndex($dir)
{
	global $index;
	foreach(scandir($dir) as $f)
	{
		if(substr($f, 0, 1) != ".")
		{
			$fn = $dir."/".$f;
			if(is_dir($fn))
			{
				recursivelyIndex($fn);
			}
			else
			{
				array_push($index, substr($fn, 2));
			}
		}
	}
}
recursivelyIndex(".");

echo "Building...\n";
function createZip($file)
{
	$zip = new ZipArchive();
	$zip->open($file, ZipArchive::CREATE + ZipArchive::EXCL + ZipArchive::CHECKCONS) or die("Failed to create {$file}.\n");
	return $zip;
}
$build = createZip("Universal Bypass.zip");
$firefox = createZip("Universal Bypass for Firefox.zip");
foreach($index as $fn)
{
	if($fn == "README.md" || $fn == "injection_script.js")
	{
		continue;
	}
	if($fn == "manifest.json")
	{
		$json = json_decode(file_get_contents($fn), true);
		$json["incognito"] = "split";
		$build->addFromString($fn, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
	else
	{
		$build->addFile($fn, $fn);
	}
	$firefox->addFile($fn, $fn);
}
$build->close();
$firefox->close();
