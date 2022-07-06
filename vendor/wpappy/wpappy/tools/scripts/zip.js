const fs = require( 'fs' );
const archiver = require( 'archiver' );
const log = require( 'log-beautify' );
const config = require( '../config' );
const name = config.name + '-' + config.version;
const output = fs.createWriteStream( config.rootPath + '/releases/' + name + '.zip' );
const archive = archiver( 'zip', {});

output.on( 'close', function() {
	console.log( '\n' );
	log.success_( '"' + name + '.zip" deployed to the releases folder' );
	console.log( '\n' )
});

archive.on( 'error', function( err ) {
	console.error( err );
});

archive.pipe( output );

let directories = config.directories;

for ( let i = 0; i < directories.length; i++ ) {
	archive.directory( config.rootPath + '/' + directories[i], name + '/' + directories[i], null );
}

let files = config.files;

for ( let i = 0; i < files.length; i++ ) {
	archive.file( config.rootPath + '/' + files[i], { name: name + '/' + files[i] });
}

archive.finalize();
