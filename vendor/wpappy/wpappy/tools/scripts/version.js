const fs = require( 'fs' );
const glob = require( 'glob' );
const log = require( 'log-beautify' );
const config = require( '../config' );
const cachePath = '../cache';
const cache = require( cachePath );
const PHPClassesRoot = 'includes';
const newPHPClassName = config.basePHPClassName + '_' + config.version.split( '.' ).join( '_' );

glob( config.rootPath + '/' + PHPClassesRoot + '/**/*.php', function( err, paths ) {
	if ( err ) {
		return console.log( err );
	}

	paths.push( config.rootPath + '/index.php' );
	paths.push( config.rootPath + '/README.md' );

	replaceFileText(
		config.rootPath + '/composer.json',
		cache.currentPHPClassName,
		newPHPClassName,
		setPackageVersion
	);

	replaceFileText(
		config.rootPath + '/index.php',
		cache.currentVersion,
		config.version
	);

	paths.forEach( function( path ) {
		replaceFileText( path, cache.currentPHPClassName, newPHPClassName, replaceAdditions );
	});

	setCache();
});

function replaceFileText( path, needle, replace, modifyResult = null ) {
	fs.readFile( path, 'utf8', function( err, data ) {
		if ( err ) {
			return console.error( err );
		}

		let result = data.replace( new RegExp( needle, 'g' ), replace );

		if ( modifyResult ) {
			result = modifyResult( result, needle, replace );
		}

		fs.writeFile( path, result, 'utf8', function( err ) {
			if ( err ) {
				return console.error( err );
			}
		});
	});
}

function setPackageVersion( data ) {
	data = JSON.parse( data );
	data.version = config.version;
	data.config['autoloader-suffix'] = '_' + newPHPClassName;

	return JSON.stringify( data, null, 2 );
}

function replaceAdditions( data, needle, replace ) {
	data = data.replace(
		new RegExp( needle.toLowerCase().replace( /_/g, '-' ), 'g' ),
		replace.toLowerCase().replace( /_/g, '-' )
	);

	return data.replace(
		new RegExp( needle.replace( /_/g, '-' ), 'g' ),
		replace.replace( /_/g, '-' )
	);
}

function setCache() {
	cache.currentVersion = config.version;
	cache.currentPHPClassName = newPHPClassName;

	fs.writeFile(
		cachePath + '.json',
		JSON.stringify( cache, null, 2 ),
		'utf8',
		function( err ) {
			if ( err ) {
				return console.error( err );
			}
		}
	);
}

log.success_( 'Version changed to ' + config.version );
