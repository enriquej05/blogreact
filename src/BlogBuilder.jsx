import React, { useState, useCallback } from 'react';
import { DndContext, closestCenter, useDraggable, useDroppable } from '@dnd-kit/core';

// Componente Draggable
function DraggableWrapper({ id, children, onDelete, isRemovable }) {
  const { attributes, listeners, setNodeRef, transform, isDragging } = useDraggable({ id });

  const style = {
    transform: transform ? `translate3d(${transform.x}px, ${transform.y}px, 0)` : undefined,
    zIndex: isDragging ? 9999 : 'auto',
    position: 'relative',
    padding: '5px',
    border: '1px solid #ccc',
    backgroundColor: 'black',
  };

  return (
    <div ref={setNodeRef} style={style}>
      <div style={{ display: 'flex' }}>
        <div
          {...listeners}
          {...attributes}
          style={{
            cursor: 'move',
            padding: '5px',
            marginRight: '10px',
            backgroundColor: '#ddd',
            borderRadius: '50%',
            width: '20px',
            height: '20px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
          }}
        >
          ⠿
        </div>
        {isRemovable && (
          <button
            onClick={(e) => {
              e.stopPropagation();
              onDelete();
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
function Subsection({ id, items, renderItem, handleDelete, isGrid }) {
  const gridStyle = isGrid
    ? {
        display: 'grid',
        gridTemplateColumns: 'repeat(3, 1fr)',
        gap: '10px',
      }
    : {};

  return (
    <Droppable id={id}>
      <div style={{ margin: '10px 0', ...gridStyle }}>
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
function EditableComponent({ type, content, onEdit, classes, onClassChange }) {
  const [isEditing, setIsEditing] = useState(false);
  const [editedContent, setEditedContent] = useState(content);
  const [editedClasses, setEditedClasses] = useState(classes || '');

  const handleDoubleClick = () => setIsEditing(true);

  const handleBlur = () => {
    setIsEditing(false);
    onEdit(editedContent);
    onClassChange(editedClasses);
  };

  if (isEditing) {
    return (
      <div tabIndex={-1} onBlur={handleBlur}>
        <input
          type="text"
          value={editedClasses}
          onChange={(e) => setEditedClasses(e.target.value)}
          placeholder="Clases de Tailwind"
          style={{ marginBottom: '10px', width: '100%' }}
        />
        <input
          type="text"
          value={editedContent}
          onChange={(e) => setEditedContent(e.target.value)}
          style={{ width: '100%' }}
        />
        <div className={editedClasses}>{editedContent}</div>
      </div>
    );
  }

  const Component = type === 'title' ? 'h2' : 'div';
  return (
    <Component onDoubleClick={handleDoubleClick} className={classes}>
      {content}
    </Component>
  );
}

// Componente principal BlogBuilder
const BlogBuilder = () => {
  const [items, setItems] = useState({
    available: ['title', 'paragraph', 'image', 'subsection', 'grid-3'], // Añadir subsección grid
    header: [],
    content: [],
    sidebar: [],
  });

  const [components, setComponents] = useState({});

  const handleDragEnd = useCallback((event) => {
    const { active, over } = event;
    if (over && active.id !== over.id) {
      setItems((prevItems) => {
        const activeContainer = Object.keys(prevItems).find((key) => prevItems[key].includes(active.id));
        const overContainer = over.id;

        if (!prevItems[overContainer]) prevItems[overContainer] = [];

        const newId = activeContainer === 'available' ? `${active.id}-${Date.now()}` : active.id;

        if (activeContainer === 'available') {
          setComponents((prev) => ({
            ...prev,
            [newId]: { type: active.id, content: `New ${active.id}` },
          }));
        }

        return {
          ...prevItems,
          [activeContainer]: activeContainer === 'available'
            ? prevItems[activeContainer]
            : prevItems[activeContainer].filter((item) => item !== active.id),
          [overContainer]: [...(prevItems[overContainer] || []), newId],
        };
      });
    }
  }, []);

  const handleEdit = useCallback((id, newContent) => {
    setComponents((prev) => ({
      ...prev,
      [id]: { ...prev[id], content: newContent },
    }));
  }, []);

  const handleClassChange = useCallback((id, newClasses) => {
    setComponents((prev) => ({
      ...prev,
      [id]: { ...prev[id], classes: newClasses },
    }));
  }, []);

  const handleDelete = useCallback((id, container) => {
    setItems((prevItems) => ({
      ...prevItems,
      [container]: prevItems[container].filter((itemId) => itemId !== id),
    }));
    setComponents((prevComponents) => {
      const { [id]: _, ...rest } = prevComponents;
      return rest;
    });
  }, []);

  const renderItem = useCallback(
    (id) => {
      const component = components[id];
      if (!component) return null;

      if (component.type === 'subsection' || component.type === 'grid-3') {
        return (
          <Subsection
            id={id}
            items={items[id] || []}
            renderItem={renderItem}
            handleDelete={handleDelete}
            isGrid={component.type === 'grid-3'}
          />
        );
      }

      return (
        <EditableComponent
          type={component.type}
          content={component.content}
          classes={component.classes}
          onEdit={(newContent) => handleEdit(id, newContent)}
          onClassChange={(newClasses) => handleClassChange(id, newClasses)}
        />
      );
    },
    [components, handleEdit, items, handleClassChange]
  );

  const handleSave = async () => {
    const getComponentData = (id) => {
      const component = components[id];
      
      if (!component) return null;
  
      // Si es una subsección o grid, debemos extraer también los items que contiene
      if (component.type === 'subsection' || component.type === 'grid-3') {
        return {
          ...component,
          items: (items[id] || []).map(getComponentData) // Recursivamente obtener los datos de sus hijos
        };
      }
  
      // Para componentes simples, devolvemos el contenido normal
      return {
        ...component
      };
    };
  
    const blogData = {
      header: items.header.map(getComponentData),
      content: items.content.map(getComponentData),
      sidebar: items.sidebar.map(getComponentData),
    };
  
    console.log('Blog data to be saved:', blogData);
    alert('Blog data logged to console. Implement API call here.');
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
              <DraggableWrapper key={id} id={id} onDelete={() => handleDelete(id, 'header')} isRemovable={true}>
                {renderItem(id)}
              </DraggableWrapper>
            ))}
          </Droppable>

          <Droppable id="content">
            <h4>Content</h4>
            {items.content.map((id) => (
              <DraggableWrapper key={id} id={id} onDelete={() => handleDelete(id, 'content')} isRemovable={true}>
                {renderItem(id)}
              </DraggableWrapper>
            ))}
          </Droppable>

          <Droppable id="sidebar">
            <h4>Sidebar</h4>
            {items.sidebar.map((id) => (
              <DraggableWrapper key={id} id={id} onDelete={() => handleDelete(id, 'sidebar')} isRemovable={true}>
                {renderItem(id)}
              </DraggableWrapper>
            ))}
          </Droppable>
        </div>
      </div>

      <button onClick={handleSave} style={{ marginTop: '20px' }}>
        Guardar Blog
      </button>
    </DndContext>
  );
};

export default BlogBuilder;
