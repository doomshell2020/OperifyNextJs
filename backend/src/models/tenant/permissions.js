const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('permissions', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    module: {
      type: DataTypes.STRING(150),
      allowNull: false
    },
    menu: {
      type: DataTypes.STRING(150),
      allowNull: false
    },
    controller: {
      type: DataTypes.STRING(150),
      allowNull: true
    },
    action: {
      type: DataTypes.STRING(150),
      allowNull: false
    },
    short_name: {
      type: DataTypes.STRING(150),
      allowNull: false
    }
  }, {
    sequelize,
    tableName: 'permissions',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
      {
        name: "module_id",
        using: "BTREE",
        fields: [
          { name: "menu" },
        ]
      },
      {
        name: "id",
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
