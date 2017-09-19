#DoctrineEncryptBundle

Bundle allows to create doctrine entities with fields that will be protected with help of some encryption algorithm in database and it will be clearly for developer, because bundle is uses doctrine life cycle events

Inspired by https://github.com/ambta/DoctrineEncryptBundle & https://github.com/vmelnik-ukraine/DoctrineEncryptBundle

##What does it do exactly

It gives you the opportunity to add the @Encrypt annotation above each string property

    /**
     * @Encrypt
     */
protected $username;

The bundle uses doctrine his life cycle events to encrypt the data when inserted into the database and decrypt the data when loaded into your entity manager. It is only able to encrypt string values at the moment, numbers and other fields will be added later on in development.

##Advantages and disadvantaged of an encrypted database

###Advantages

    Information is stored safely
    Not worrying about saving backups at other locations
    Unreadable for employees managing the database

###Disadvantages

    Can't use ORDER BY on encrypted data
    In SELECT WHERE statements the where values also have to be encrypted
    When you lose your key you lose your data (Make a backup of the key on a safe location)

##Documentation

This bundle is responsible for encryption/decryption of the data in your database. All encryption/decryption work on the server side.

The following documents are available:

####Installation

#####Step 1: Download ResomediaDoctrineEncryptBundle using composer

ResomediaDoctrineEncryptBundle should be installed usin Composer:

    {
        "require": {
            "resomedia/doctrine-encrypt-bundle": "1.*"
        }
    }

Now tell composer to download the bundle by running the command:

$ php composer.phar update resomedia/doctrine-encrypt-bundle


#####Step 2: Enable the bundle

Enable the bundle in the Symfony2 kernel by adding it in your /app/AppKernel.php file:

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Resomedia\DoctrineEncryptBundle\ResomediaDoctrineEncryptBundle(),
        );
    }

####Configuration

There are 4 paramaters in the configuration of the Doctrine encryption bundle.

    secret_key - The key used to encrypt the data
        Default: empty, the bundle will use your Symfony2 secret key.

    protocol - The cipher method used to encrypt the data (list all with openssl_get_cipher_methods)

    encryptor_class - Custom class for encrypting data
        Encryptor class, your own encryptor class will override encryptor paramater
        Default: empty
        
    iv - initialization vector (you can calculate is length for your cipher with openssl_cipher_iv_length)

yaml

    resomedia_doctrine_encrypt:
        secret_key:           AB1CD2EF3GH4IJ5KL6MN7OP8QR9ST0UW # Your own key
        encryptor:            AES-128-CBC / AES-128-ECB...
        iv:                   34857d973953e44a # random string whith length = openssl_cipher_iv_length(protocol)
        encryptor_class:      \Resomedia\DoctrineEncryptBundle\Encryptors\YourOwnEncryptor # your own encryption class
    
!!! write this parameters in your parameters.yml not directly in config.yml. !!!

####Usage

Add @Encrypted annotation

    namespace Acme\DemoBundle\Entity;
    
    use Doctrine\ORM\Mapping as ORM;
    
    // importing @Encrypted annotation
    use Resomedia\DoctrineEncryptBundle\Configuration\Encrypted;
    
    /**
     * @ORM\Entity
     * @ORM\Table(name="user")
     */
    class User {
        
        ..
        
        /**
         * @ORM\Column(type="string", name="email")
         * @Encrypted
         * @var int
         */
        private $email;
       
        ..
    
    }

####Console commands

#####Encrypt / decrypt data

Encrypt / decrypt a data specified in argument.

    php bin/console doctrine:encrypt:data test
    
    php bin/console doctrine:decrypt:data XXXX
    
2 argument :

-The data you want to encrypt/decrypt.

-The encryptor you want to decrypt the data with (optional)

#####Encrypt / decrypt database

Encrypt / decrypt all datas in database with field have @Encrypted annotation if isn't already encrypt / decrypt.

    php bin/console doctrine:encrypt:database
    
    php bin/console doctrine:decrypt:database
    
2 argument :

-The encryptor you want to decrypt the data with (optional)

-The batchSize, number of row encrypt / decrypt between two flush (optional | default : 200)

####Custom encryption class

Create your own class that implement EncryptorInterfaces and specify it in encryptor_class parameter.

###License

This bundle is under the MIT license. See the complete license in the bundle

###Versions

I'm using Semantic Versioning like described here
