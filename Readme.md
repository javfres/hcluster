
# Hierarchical clustering

This PHP library implements some algorithms of HCA.
See [Wikipedia](https://en.wikipedia.org/wiki/Hierarchical_clustering).
The algorithms are: *Complete linkage clustering*,
*Single-linkage clustering*, and *Average linkage clustering*.
The implementations are the basic *O(n^3)*.

## Composer

Require from github

```
{
    "repositories": [
        {
            "type": "git",
            "url": "https://github.com/javfres/hcluster"
        }
    ],
    "require": {
        "javfres/hcluster": "dev-master"
    }
}
```


Composer for local repository

{
	"repositories": [
		{
		    "type": "path",
		    "url": "/home/javier/Desktop/cluster/hcluster"
		}
	],
    "require": {
        "javfres/hcluster": "@dev"
    }
}





## Examples

The folder *examples* contain some usage examples.
You can find a implementation of the examples used
in the wikipedia page.

`php examples/wikipedia/main.php`

And some other examples using custom item and distance objects:

`php examples/samples/main.php`

`php examples/samples/main_random.php`
