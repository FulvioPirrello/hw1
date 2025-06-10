/*api 1. serve per vedere immagini di file 3d, diverse ogni volta che la pagina si ricarica.
const SKETCHFAB_API = "https://api.sketchfab.com/v3/models";
const API_TOKEN= "secret";
const FAKE_STORE_API = "https://fakestoreapi.com/products";

async function modelli_sketchfab() {
    try {
        const response = await fetch(`${SKETCHFAB_API}?type=downloadable&tags=technology&random_seed=${Math.random()}`, {
        });

        if (!response.ok) {
            throw new Error("Errore durante il recupero dei modelli da Sketchfab");
        }

        const data = await response.json();
        return data.results;
    } catch (error) {
        console.error("Errore:", error);
        return [];
    }
}

async function sketchfab_item_img() {
    const models = await modelli_sketchfab();
    const items = document.querySelectorAll(".img-item");

    items.forEach((item, index) => {
        if (models[index]) {
            item.src = models[index].thumbnails.images[0].url;
        }
    });
}

async function fetch_Stampanti_3D() {
    try {
        const response = await fetch(FAKE_STORE_API);

        if (!response.ok) {
            throw new Error("Errore durante il recupero dei prodotti da Fake Store API");
        }

        const products = await response.json();
        return products.filter(product => product.category === "electronics"); 
    } catch (error) {
        console.error("Errore:", error);
        return [];
    }
}*/
//api 2, serve per mostrare degli articoli elettronici inerenti a pc e stampanti 3d
async function carica_accessori() {
    const container = document.getElementById('gadget-container');
    if (!container) return;
  
    try {
      const response = await fetch('https://fakestoreapi.com/products');
      const products = await response.json();
  
      const printers = products.filter(product =>
        product.title.toLowerCase().includes('3d_printer') ||
        product.description.toLowerCase().includes('3d_printer') ||
        product.category.toLowerCase().includes('electronics')
      ).slice(0, 6);
  
      printers.forEach(printer => 
        {
        const item = document.createElement('div');
        item.className = 'item';
  
        item.innerHTML = `
          <img src="${printer.image}" alt="${printer.title}" class="img-item" />
          <div class="name">${printer.title}</div>`
          ;
  
        container.appendChild(item);
      });
    } catch (error) 
    {
      console.error('Errore durante il caricamento delle stampanti 3D:', error);
    }
  }
  
async function carica_accessori() {
    const gadget_Items = document.querySelectorAll('.gadget-item');
    if (gadget_Items.length === 0) return;

    try {
        const response = await fetch('https://fakestoreapi.com/products');
        if (!response.ok) {
            throw new Error("Errore durante il recupero dei prodotti da Fake Store API");
        }
        const products = await response.json();

        const printers = products.filter(product =>
            product.title.toLowerCase().includes('3d_printer') ||
            product.description.toLowerCase().includes('3d_printer') ||
            product.category.toLowerCase().includes('electronics')
        ).slice(0, 6);

        printers.forEach((printer, index) => {
            if (gadget_Items[index]) {
                const img = document.createElement('img');
                img.src = printer.image;
                img.alt = printer.title;
                img.className = 'gadget-img';

                const title = document.createElement('div');
                title.className = 'gadget-name';
                title.textContent = printer.title;

                gadget_Items[index].textContent = '';
                gadget_Items[index].appendChild(img);
                gadget_Items[index].appendChild(title);
            }
        });
    } catch (error) {
        console.error("Errore durante il caricamento delle stampanti 3D:", error);
    }
}

document.addEventListener('DOMContentLoaded', async () => {
    try 
    {
      await carica_accessori();
                await sketchfab_item_img();
    } catch (error) {
        console.error("Errore durante il caricamento dei dati:", error);
    }
});


