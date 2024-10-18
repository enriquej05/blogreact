import React, { useState, useCallback } from 'react';
import { DndContext, closestCenter, useDraggable, useDroppable } from '@dnd-kit/core';

// Componente Draggable - aquí renderizamos lo que se mostrara
function DraggableWrapper({ id, children, onDelete, isRemovable }) {
  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({ id });

  // Estilo para el área draggable
  const style = {
    transform: transform ? `translate3d(${transform.x}px, ${transform.y}px, 0)` : undefined,
    zIndex: isDragging ? 9999 : 'auto', // Aumentar z-index cuando se arrastra
    position: 'relative',
    display: 'block', // Para alinear ícono de arrastre, contenido y botón
    alignItems: 'center',
    padding: '5px',
    border: '1px solid #ccc',
    backgroundColor: 'black',
  };

  return (
    <div ref={setNodeRef} style={style}>
      <div style={{display: 'flex'}}>
      {/* Ícono de arrastre */}
      <div
            {...listeners}
            {...attributes}
            style={{
              cursor: 'move',
              padding: '5px',
              marginRight: '10px',
              backgroundColor: '#ddd', // Ícono visible
              borderRadius: '50%',
              width: '20px',
              height: '20px',
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
            }}
          >
            ⠿ {/* Puedes usar un ícono o símbolo de arrastre aquí */}
          </div>

      

      {/* Botón de eliminar */}
      {isRemovable && (
        <button
          onClick={(e) => {
            e.stopPropagation(); // Evitar que el botón active drag-and-drop
            onDelete(); // Ejecutar la eliminación
          }}
          style={{
            background: 'red',
            color: 'white',
            border: 'none',
            cursor: 'pointer',
            marginLeft: '10px',
          }}
        >
          X
        </button>
      )}
      </div>
    
      {/* Contenido editable o arrastrable */}
      <div style={{ flexGrow: 1 }}>{children}</div>
    </div>
  );
}



// Componente Droppable
function Droppable({ id, children }) {
  const { setNodeRef } = useDroppable({ id });

  return (
    <div ref={setNodeRef} style={{ minHeight: '100px', border: '1px dashed gray', margin: '10px 0', padding: '10px' }}>
      {children}
    </div>
  );
}

// Componente Subsection
function Subsection({ id, items, renderItem, handleDelete }) { // Añadimos handleDelete como prop
  return (
    <Droppable id={id}>
      <div style={{margin: '10px 0'}}>
        {items.map((itemId) => (
          <DraggableWrapper key={itemId} id={itemId} isRemovable={true} onDelete={() => handleDelete(itemId, id)}>
            {renderItem(itemId)}
          </DraggableWrapper>
        ))}
      </div>
    </Droppable>
  );
}



// Componente Editable
function EditableComponent({ type, content, onEdit }) {
  const [isEditing, setIsEditing] = useState(false);
  const [editedContent, setEditedContent] = useState(content);
  const [imagePreview, setImagePreview] = useState(type === 'image' ? content : ''); // Para las imágenes

  const handleDoubleClick = (e) => {
    e.stopPropagation();
    setIsEditing(true);
  };

  const handleBlur = () => {
    setIsEditing(false);
    if (type !== 'image') {
      onEdit(editedContent); // Guardar el contenido del texto cuando pierde el foco
    }
  };

  const handleChange = (e) => {
    setEditedContent(e.target.value); // Actualizar el contenido de texto mientras se escribe
  };

  const handleKeyDown = (e) => {
    // Prevenir que otros manejadores procesen Enter
    if (e.key === 'Enter') {
      e.preventDefault();  // Evita el comportamiento predeterminado del Enter
      e.stopPropagation(); // Evita la propagación para prevenir efectos en Draggable
      handleBlur(); // Guarda cuando se presiona Enter
    }
    
    
    // Permitir espacios en blanco
    if (e.key === ' ') {
      e.preventDefault(); // Evita el comportamiento predeterminado
      setEditedContent(prev => prev + ' '); // Agrega un espacio manualmente
    }
  };

  const handleImageChange = (e) => {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result); // Mostrar la vista previa de la imagen
        onEdit(reader.result); // Guardar la imagen codificada en base64 en el estado
      };
      reader.readAsDataURL(file); // Convertir el archivo en base64
    }
  };

  if (isEditing) {
    // Si el tipo es imagen, mostramos un input de archivo
    if (type === 'image') {
      return (
        <div>
          <input type="file" onChange={handleImageChange} />
          {imagePreview && <img src={imagePreview} alt="Vista previa" style={{ width: '100%', marginTop: '10px' }} />}
        </div>
      );
    }
    return (
      <input
        type="text"
        value={editedContent}
        onChange={handleChange} // Actualiza el estado mientras se escribe
        onBlur={handleBlur} // Guarda los cambios al perder el foco
        onKeyDown={handleKeyDown} // Detecta Enter y espacios
        autoFocus
        style={{ width: '100%', padding: '5px' }}
      />
    );
  }

  // Para imágenes, mostramos una vista previa cuando no está en modo de edición
  if (type === 'image') {
    return (
      <div onDoubleClick={handleDoubleClick}>
        {imagePreview ? (
          <img src={imagePreview} alt="Imagen" style={{ width: '100%' }} />
        ) : (
          <p>Haz doble clic para subir una imagen</p>
        )}
      </div>
    );
  }

  // Para otros tipos de componentes (título, párrafo)
  const Component = type === 'title' ? 'h2' : type === 'paragraph' ? 'p' : 'div';
  return <Component onDoubleClick={handleDoubleClick}>{content}</Component>;
}


const BlogBuilder = () => {
  const [items, setItems] = useState({
    available: ['title', 'paragraph', 'image', 'subsection'], // Añadimos el tipo subsection
    header: [],
    content: [],
    sidebar: [],
  });

  const [components, setComponents] = useState({});

  const handleDragEnd = useCallback((event) => {
    const { active, over } = event;
  
    if (over && active.id !== over.id) {
      setItems((prevItems) => {
        const activeContainer = Object.keys(prevItems).find(
          (key) => prevItems[key].includes(active.id)
        );
        const overContainer = over.id;
  
        // Verificar si el contenedor de destino es undefined, inicializarlo si es necesario
        if (!prevItems[overContainer]) {
          prevItems[overContainer] = []; // Inicializamos como un array vacío
        }
  
        if (!activeContainer || !overContainer || activeContainer === overContainer) {
          return prevItems;
        }
  
        const newId = activeContainer === 'available' ? `${active.id}-${Date.now()}` : active.id;
  
        if (activeContainer === 'available') {
          setComponents(prev => ({
            ...prev,
            [newId]: { type: active.id, content: `New ${active.id}` }
          }));
        }
  
        return {
          ...prevItems,
          [activeContainer]: activeContainer === 'available' 
            ? prevItems[activeContainer] 
            : prevItems[activeContainer].filter((item) => item !== active.id),
          [overContainer]: [...(prevItems[overContainer] || []), newId], // Asegurarse de que siempre sea un array
        };
      });
    }
  }, []);
  
  

  const handleEdit = useCallback((id, newContent) => {
    setComponents(prev => ({
      ...prev,
      [id]: { ...prev[id], content: newContent }
    }));
  }, []);

  const handleDelete = useCallback((id, container) => {
    console.log(id);
    
    setItems((prevItems) => ({
      ...prevItems,
      [container]: prevItems[container].filter(itemId => itemId !== id),
    }));
    setComponents((prevComponents) => {
      const { [id]: _, ...rest } = prevComponents; // Elimina el componente de los componentes
      return rest;
    });
  }, []);

  const renderItem = useCallback((id) => {
    const component = components[id];
    if (!component) return null;
  
    if (component.type === 'subsection') {
      // Renderizar la subsección con los componentes que contiene
      return (
        <Subsection
          id={id}
          items={items[id] || []} // Subcomponentes dentro de la subsección
          renderItem={renderItem}
          handleDelete={handleDelete} // Pasar la función handleDelete
        />
      );
    }
  
    return (
      <EditableComponent
        type={component.type}
        content={component.content}
        onEdit={(newContent) => handleEdit(id, newContent)}
      />
    );
  }, [components, handleEdit, items]);
  
  

  const handleSave = async () => {
    const blogData = {
      header: items.header.map(id => components[id]),
      content: items.content.map(id => components[id]),
      sidebar: items.sidebar.map(id => components[id]),
    };

    console.log('Blog data to be saved:', blogData);
    alert('Blog data logged to console. Implement API call here.');

    // Uncomment the following code when you have set up your API endpoint
    /*
    try {
      const response = await fetch('/api/saveBlog', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(blogData),
      });

      if (!response.ok) {
        throw new Error('Network response was not ok');
      }

      const result = await response.json();
      console.log('Blog saved successfully:', result);
      alert('Blog guardado exitosamente!');
    } catch (error) {
      console.error('Error saving blog:', error);
      alert('Error al guardar el blog. Por favor, intenta de nuevo.');
    }
    */
  };

  return (
    <DndContext onDragEnd={handleDragEnd} collisionDetection={closestCenter}>
      <div style={{ display: 'flex', justifyContent: 'space-between' }}>
        <div style={{ border: '1px solid #ccc', padding: '10px' }}>
          <h3>Available Components</h3>
          <div>
            {items.available.map((id) => (
              <DraggableWrapper key={id} id={id} isRemovable={false}>
                <button style={{ width: '100%' }}>{id}</button>
              </DraggableWrapper>
            ))}
          </div>
        </div>

        <div style={{ flex: 1, margin: '0 20px' }}>
          <h3>Blog Layout</h3>
          <Droppable id="header">
            <h4>Header</h4>
            {items.header.map((id) => (
              <DraggableWrapper
                key={id}
                id={id}
                onDelete={() => handleDelete(id, 'header')} // Agregar función eliminar
                isRemovable={true} // Mostrar botón de eliminar
              >
                {renderItem(id)}
              </DraggableWrapper>
            ))}
          </Droppable>
          <div style={{ display: 'block' }}>
          <Droppable id="content">
          {items.content.map((id) => (
            <DraggableWrapper
              key={id}
              id={id}
              onDelete={() => handleDelete(id, 'content')}
              isRemovable={true}
            >
              {renderItem(id)}
            </DraggableWrapper>
          ))}
        </Droppable>

            <Droppable id="sidebar" style={{ flex: 1 }}>
              <h4>Sidebar</h4>
              {items.sidebar.map((id) => (
                <DraggableWrapper
                  key={id}
                  id={id}
                  onDelete={() => handleDelete(id, 'sidebar')} // Agregar función eliminar
                  isRemovable={true}
                >
                  {renderItem(id)}
                </DraggableWrapper>
              ))}
            </Droppable>
          </div>
        </div>
      </div>
      <button onClick={handleSave} style={{ marginTop: '20px' }}>Guardar Blog</button>
    </DndContext>
  );
};

export default BlogBuilder;
